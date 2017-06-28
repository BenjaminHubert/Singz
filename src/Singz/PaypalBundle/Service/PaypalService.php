<?php

namespace Singz\PaypalBundle\Service;

use PayPal\Api\Amount;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use Doctrine\ORM\EntityManager;
use PayPal\Api\PaymentExecution;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PaypalService
{
	private $clientId;
	private $clientSecret;
	private $em;
	
	public function __construct($clientId, $clientSecret, EntityManager $em)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
		$this->em = $em;
	}
	
	/**
	 * 
	 * @return \PayPal\Rest\ApiContext
	 */
	private function getApiContext()
	{
		$credential = new OAuthTokenCredential($this->clientId, $this->clientSecret);
		$apiContext = new ApiContext($credential);
		
		return $apiContext;
	}
	
	/**
	 * 
	 * @param ItemList $itemList
	 * @param float $totalAmount
	 * @param string $currency
	 * @param string $description
	 * @param string $returnUrl
	 * @param string $cancelUrl
	 * @return \PayPal\Api\Payment
	 */
	public function createPayment(ItemList $itemList, float $totalAmount, string $currency, string $description, string $returnUrl, string $cancelUrl)
	{
		/*
		 * Payer
		 *
		 * A resource representing a Payer that funds a payment For paypal account payments, set payment method to 'paypal'.
		 */
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		
		/*
		 * Amount
		 *
		 * Lets you specify a payment amount. You can also specify additional details such as shipping, tax.
		 */
		$amount = new Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($totalAmount);
		
		/*
		 * Transaction
		 *
		 * A transaction defines the contract of a payment - what is the payment for and who is fulfilling it.
		 */
		$transaction = new \PayPal\Api\Transaction();
		$transaction->setAmount($amount);
		$transaction->setItemList($itemList);
		$transaction->setDescription($description);
		$transaction->setInvoiceNumber(uniqid());

		/*
		 * Redirect urls
		 *
		 * Set the urls that the buyer must be redirected to after payment approval/ cancellation.
		 */
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($returnUrl);
		$redirectUrls->setCancelUrl($cancelUrl);

		/*
		 * Payment
		 *
		 * A Payment Resource; create one using the above types and intent set to 'sale'
		 */
		$payment = new \PayPal\Api\Payment();
		$payment->setIntent('sale');
		$payment->setPayer($payer);
		$payment->setRedirectUrls($redirectUrls);
		$payment->setTransactions(array($transaction));

		/*
		 * Get the api context
		 */
		$apiContext = $this->getApiContext();
		
		/*
		 * Create Payment
		 *
		 * Create a payment by calling the 'create' method passing it a valid apiContext.
		 * The return object contains the state and the url to which the buyer must be redirected to
		 * for payment approval
		 */
		$payment->create($apiContext);

		/*
		 * Keep the payment into the database
		 */
		$paymentEntity = new \Singz\PaypalBundle\Entity\Payment();
		$paymentEntity->setIntent($payment->getIntent());
		$paymentEntity->setState($payment->getState());
		$paymentEntity->setPaypalId($payment->getId());
		$this->em->persist($paymentEntity);
		
		$transactionEntity = new \Singz\PaypalBundle\Entity\Transaction();
		$transactionEntity->setAmount($amount->getTotal());
		$transactionEntity->setCurrency($amount->getCurrency());
		$transactionEntity->setDescription($transaction->getDescription());
		$transactionEntity->setInvoiceNumber($transaction->getInvoiceNumber());
		$transactionEntity->setPayment($paymentEntity);
		$this->em->persist($transactionEntity);
		
		$this->em->flush();		
		
		/*
		 * Get redirect url
		 *
		 * The API response provides the url that you must redirect the buyer to.
		 * Retrieve the url from the $payment->getApprovalLink() method
		 */
		return $payment;
	}
	
	
	public function executePayment(string $paymentId, string $payerId)
	{
		/*
		 * Check if the payment is already created into the database
		 */
		$paymentEntity = $this->em->getRepository('SingzPaypalBundle:Payment')->findOneBy(array(
			'paypalId' => $paymentId,
		));
		if(!$paymentEntity){
			throw new NotFoundHttpException('The payment does not exist.');
		}
		if($paymentEntity->getState() != 'created'){
			throw new HttpException(400, 'Payment has been done already for this transaction.');
		}
		
		/*
		 * Get the api context
		 */
		$apiContext = $this->getApiContext();
		
		/*
		 * Get the payment Object by passing paymentId 
		 */
		$payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
		
		/*
		 * Payment Execute
		 * 
		 * PaymentExecution object includes information necessary to execute a PayPal account payment. 
		 * The payer_id is added to the request query parameters when the user is redirected from paypal 
		 * back to your site
		 */
		$execution = new PaymentExecution();
		$execution->setPayerId($payerId);
		
		/*
		 * Execute the payment
		 */
		try {
			$result = $payment->execute($execution, $apiContext);
			try {
				$payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
			}catch(\Exception $e){
				$data = json_decode($e->getData());
				throw new \Exception($data->message);
			}
		}catch(\Exception $e){
			$data = json_decode($e->getData());
			throw new \Exception($data->message);
		}
		
		/*
		 * Update the payment into database
		 */
		$paymentEntity->setState($payment->getState());
		$this->em->persist($paymentEntity);
		$this->em->flush();
		
		return $payment;
	}
}