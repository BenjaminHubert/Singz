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

class PaypalService
{
	public function createPayment(Contribution $contribution)
	{
	}
}