<?php

namespace Singz\PaypalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
	public function executePaymentAction(Request $request, $success)
	{
		// If the payment is cancelled, display an error message
		if($success == 'false'){
			$this->addFlash('danger', 'Vous avez annulé le paiement. Votre contribution n\'a pas été prise en compte.');
			return $this->redirectToRoute('singz_index');
		}
		// Get required params
		$paymentId = $request->query->get('paymentId');
		$payerId = $request->query->get('PayerID');
		// Check if required params are provided
		if(!$paymentId || !$payerId){
			throw $this->createNotFoundException();
		}
		// Execute payment on Paypal
		$paypalService = $this->container->get('singz.paypal.paypal');
		try{
			$paypalService->executePayment($paymentId, $payerId);
		}catch(\Exception $e){
			$this->addFlash('danger', $e->getMessage());
			return $this->redirectToRoute('singz_index');
		}
		// Get the Payment entity
		$em = $this->getDoctrine()->getManager();
		$payment = $em->getRepository('SingzPaypalBundle:Payment')->findOneBy(array(
			'paypalId' => $paymentId
		));
		if(!$payment){
			throw new \Exception('Payment does not exist');
		}
		// Get the Contribution entity
		$contribution = $em->getRepository('SingzCoreBundle:Contribution')->findOneBy(array(
			'payment' => $payment
		));
		if(!$contribution){
			throw new \Exception('Contribution does not exist');
		}
		// Validating the contribution
		$contribution->setIsValidated(true);
		$em->persist($contribution);
		$em->flush();
		// Display success message
		$this->addFlash('success', 'Votre contribution au projet est validé. Merci pour votre participation !');
		return $this->redirectToRoute('singz_index');
	}
}
