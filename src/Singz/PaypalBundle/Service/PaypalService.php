<?php

namespace Singz\PaypalBundle\Service;

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use Singz\CoreBundle\Entity\Contribution;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalService
{
	private $router;
	
	public function __construct(Router $router)
	{
		$this->router = $router;
	}
	public function createPayment(Contribution $contribution)
	{
		/*
		 * Payer
		 *
		 * A resource representing a Payer that funds a payment For paypal account payments, set payment method to 'paypal'.
		 */
		$payer = new Payer();
		$payer->setPaymentMethod('paypal');
		
		/*
		 * Itemized information
		 *
		 * (Optional) Lets you specify item wise information
		 */
		$item = new Item();
		$item->setCurrency('EUR'); // List of currencies : https://developer.paypal.com/docs/integration/direct/rest/currency-codes/
		$item->setDescription(sprintf(
			'Vous souhaitez participer au projet <b>%s</b> créé par <b>%s</b> à hauteur de <b>%s</b>',
			$contribution->getProject()->getName(),
			$contribution->getProject()->getRequester()->getUsername(),
			$contribution->getAmount()
		));
		$item->setName('Contribution');
		$item->setPrice($contribution->getAmount());
		$item->setQuantity(1);
		$item->setTax(0);
		
		$itemList = new ItemList();
		$itemList->setItems(array($item));

		/*
		 * Amount
		 *
		 * Lets you specify a payment amount. You can also specify additional details such as shipping, tax.
		 */
		$amount = new Amount();
		$amount->setCurrency('EUR');
		$amount->setTotal($contribution->getAmount());

		/*
		 * Transaction
		 *
		 * A transaction defines the contract of a payment - what is the payment for and who is fulfilling it.
		 */
		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setItemList($itemList);
		$transaction->setDescription(sprintf(
			'Vous souhaitez participer au projet <b>%s</b> créé par <b>%s</b> à hauteur de <b>%s</b>',
			$contribution->getProject()->getName(),
			$contribution->getProject()->getRequester()->getUsername(),
			$contribution->getAmount()
		));
		$transaction->setInvoiceNumber(uniqid());

		/*
		 * Redirect urls
		 *
		 * Set the urls that the buyer must be redirected to after payment approval/ cancellation.
		 */
		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl($this->router->generate('singz_paypal_bundle_execute_payment', array('success' => 'true'), UrlGeneratorInterface::ABSOLUTE_URL));
		$redirectUrls->setCancelUrl($this->router->generate('singz_paypal_bundle_execute_payment', array('success' => 'false'), UrlGeneratorInterface::ABSOLUTE_URL));

		/*
		 * Payment
		 *
		 * A Payment Resource; create one using the above types and intent set to 'sale'
		 */
		$payment = new Payment();
		$payment->setIntent('sale');
		$payment->setPayer($payer);
		$payment->setRedirectUrls($redirectUrls);
		$payment->setTransactions(array($transaction));

		/*
		 * Create Payment
		 *
		 * Create a payment by calling the 'create' method passing it a valid apiContext.
		 * The return object contains the state and the url to which the buyer must be redirected to
		 * for payment approval
		 */
		$clientId = '';
		$clientSecret = '';
		$credential = new OAuthTokenCredential($clientId, $clientSecret);
		$requestId = '';
		$apiContext = new ApiContext($credential, $requestId);
		$payment->create($apiContext);

		/*
		 * Get redirect url
		 *
		 * The API response provides the url that you must redirect the buyer to.
		 * Retrieve the url from the $payment->getApprovalLink() method
		 */
		return $payment;
	}
}