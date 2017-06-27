<?php

namespace Singz\PaypalBundle\Service;

use PayPal\Api\Amount;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalService
{
	private $clientId;
	private $clientSecret;
	
	public function __construct($clientId, $clientSecret)
	{
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret;
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
		$transaction = new Transaction();
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
		$credential = new OAuthTokenCredential($this->clientId, $this->clientSecret);
		$apiContext = new ApiContext($credential);
		$payment->create($apiContext);

		/*
		 * Keep the payment into the database
		 */
		
		
		/*
		 * Get redirect url
		 *
		 * The API response provides the url that you must redirect the buyer to.
		 * Retrieve the url from the $payment->getApprovalLink() method
		 */
		return $payment;
	}
}