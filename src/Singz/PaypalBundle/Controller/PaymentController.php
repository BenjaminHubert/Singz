<?php

namespace Singz\PaypalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
	public function executePaymentAction($success)
	{
		return new Response();
	}
}
