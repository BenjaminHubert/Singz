<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Singz\CoreBundle\Entity\Project;
use Singz\CoreBundle\Form\ProjectType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Singz\CoreBundle\Form\ContributionType;
use Singz\CoreBundle\Entity\Contribution;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ProjectController extends Controller
{
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
	public function newAction(Request $request)
	{
		//Get the entity manager
		$em = $this->getDoctrine()->getManager();
		//Get user's project
		$project = $em->getRepository('SingzCoreBundle:Project')->findOneBy(array(
			'requester' => $this->getUser(),
			'state' => Project::STATE_VISIBLE
		));
		if($project){
			$this->addFlash('warning', 'Vous avez déjà un projet en cours');
			return $this->redirectToRoute('singz_user_bundle_homepage', array('username' => $this->getUser()->getUsername()));
		}
		// Create new Project
		$project = new Project();
		// Create form
		$form = $this->createForm(ProjectType::class, $project);
		$form->remove('requester');
		$form->handleRequest($request);
		// Form submission
		if($form->isSubmitted() && $form->isValid()){
			// Set the current user as the requester
			$project->setRequester($this->getUser());
			// Insert into db
			$em->persist($project);
			$em->flush();
			// Display success
    		$this->addFlash('success', 'Projet créé avec succès.');
			// On redirige vers la page de visualisation du projet nouvellement créé
			return $this->redirectToRoute('singz_user_bundle_homepage', array(
				'username' => $this->getUser()->getUsername()
			));
		}
		// Render the view
		return $this->render('SingzCoreBundle:Project:new.html.twig', array(
			'form' => $form->createView()
		));
	}
	
	public function showAction(Request $request, $id)
	{
		//Get the entity manager
		$em = $this->getDoctrine()->getManager();
		// Get the project
		$project = $em->getRepository('SingzCoreBundle:Project')->findOneBy(array(
			'id' => $id,
		));
		if($project == null) {
			throw $this->createNotFoundException('Project inexistant');
		}
		//Create contribution form
		$contribution = new Contribution();
		$form = $this->createForm(ContributionType::class, $contribution, array(
			'action' => $this->generateUrl('singz_core_bundle_project_contributing', array('id' => $project->getId()))
		));
		$form->remove('project');
		$form->remove('contributer');
		// Sort contribution by date
		$contributions = $project->getContributions()->toArray();
		usort($contributions, function($a, $b){
			return $b->getCreatedAt() <=> $a->getCreatedAt();
		});
		// Render the view
		return $this->render('SingzCoreBundle:Project:show.html.twig', array(
			'project' => $project,
			'form' => $form->createView(),
			'contributions' => $contributions
		));
	}

	/**
	 * @Security("has_role('ROLE_USER')")
	 */
	public function editAction(Request $request, $id)
	{
		//Get the entity manager
		$em = $this->getDoctrine()->getManager();
		// Get the project
		$project = $em->getRepository('SingzCoreBundle:Project')->findOneBy(array(
			'id' => $id,
		));
		if($project == null) {
			throw $this->createNotFoundException('Project inexistant');
		}
		// Check authorization
		if($this->getUser() != $project->getRequester() && !$this->isGranted('ROLE_ADMIN')){
			throw new AccessDeniedException('Accès refusé');
		}
		// Create form
		$form = $this->createForm(ProjectType::class, $project);
		$form->remove('requester');
		$form->handleRequest($request);
		// Form submission
		if($form->isSubmitted() && $form->isValid()){
			// Update db
			$em->persist($project);
			$em->flush();
			// Display success
    		$this->addFlash('success', 'Projet mis à jour');
			// On redirige vers la page du projet 
			return $this->redirectToRoute('singz_core_bundle_project_show', array(
				'id' => $project->getId()
			));
		}
		// Render the view
		return $this->render('SingzCoreBundle:Project:edit.html.twig', array(
			'project' => $project,
			'form' => $form->createView()
		));
	}
	
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
	public function deleteAction(Request $request, $id)
	{
		//Get the entity manager
		$em = $this->getDoctrine()->getManager();
		// Get the project
		$project = $em->getRepository('SingzCoreBundle:Project')->findOneBy(array(
			'id' => $id,
		));
		if($project == null) {
			throw $this->createNotFoundException('Project inexistant');
		}
		// Check authorization
		if($this->getUser() != $project->getRequester() && !$this->isGranted('ROLE_ADMIN')){
			throw new AccessDeniedException('Accès refusé');
		}
		// Check the current state
		if($project->getState() == Project::STATE_DELETED ){
			// Display error
			$this->addFlash('danger', 'Le projet a déjà été supprimé');
			// On redirige vers la page du projet
			return $this->redirectToRoute('singz_core_bundle_project_show', array(
				'id' => $project->getId()
			));
		}
		// Set the project as deleted
		$project->setState(Project::STATE_DELETED);
		// Update db
		$em->persist($project);
		$em->flush();
		// Display success
		$this->addFlash('success', 'Projet supprimé avec succès');
		// On redirige vers la page du projet
		return $this->redirectToRoute('singz_core_bundle_project_show', array(
			'id' => $project->getId()
		));
	}
	
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
	public function contributingAction(Request $request, $id)
	{
		//Get the entity manager
		$em = $this->getDoctrine()->getManager();
		// Get the project
		$project = $em->getRepository('SingzCoreBundle:Project')->findOneBy(array(
			'id' => $id,
		));
		if($project == null) {
			throw $this->createNotFoundException('Projet inexistant');
		}
		// Create form
		$contribution = new Contribution();
		$form = $this->createForm(ContributionType::class, $contribution);
		$form->remove('project');
		$form->remove('contributer');
		$form->handleRequest($request);
		// Check if submitted
		if(!$form->isSubmitted()){
			$this->addFlash('danger', 'Le formulaire doit être soumis.');
			return $this->redirectToRoute('singz_core_bundle_project_show', array(
				'id' => $project->getId()
			));
		}
		// Check if the form is valid
		if(!$form->isValid()){
			$this->addFlash('danger', 'Le formulaire n\'est pas valide.');
			return $this->redirectToRoute('singz_core_bundle_project_show', array(
				'id' => $project->getId()
			));
		}
		// Create contribution
		$contribution->setProject($project);
		$contribution->setContributer($this->getUser());
		
		// Paypal item
		$currency = 'EUR';
		$description = sprintf(
			'Vous souhaitez participer au projet "%s" créé par "%s" à hauteur de "%s" '.$currency,
			$contribution->getProject()->getName(),
			$contribution->getProject()->getRequester()->getUsername(),
			$contribution->getAmount()
		);
		$item = new Item();
		$item->setCurrency($currency); // List of currencies : https://developer.paypal.com/docs/integration/direct/rest/currency-codes/
		$item->setDescription($description);
		$item->setName('Contribution au projet "'.$contribution->getProject()->getName().'"');
		$item->setPrice($contribution->getAmount());
		$item->setQuantity(1);
		$itemList = new ItemList();
		$itemList->setItems(array($item));
		
		// Call Paypal service
		$paypalService = $this->container->get('singz.paypal.paypal');
		try{
			$payment = $paypalService->createPayment(
				$itemList,
				$contribution->getAmount(),
				$currency,
				$description,
				$this->generateUrl('singz_paypal_bundle_execute_payment', array('success' => 'true'), UrlGeneratorInterface::ABSOLUTE_URL),
				$this->generateUrl('singz_paypal_bundle_execute_payment', array('success' => 'false'), UrlGeneratorInterface::ABSOLUTE_URL)
			);
		}catch(\Exception $e){
			$this->addFlash('danger', 'Une erreur a été rencontrée ('.$e->getMessage().')');
			// Redirect to route
			return $this->redirectToRoute('singz_core_bundle_project_show', array(
				'id' => $project->getId()
			));
		}
		
		// Create a contribution
		$paymentEntity = $em->getRepository('SingzPaypalBundle:Payment')->findOneBy(array(
			'paypalId' => $payment->getId()
		));
		if(!$paymentEntity){
			throw new \Exception('Payment does not exist');
		}
		$contribution->setPayment($paymentEntity);
		$em->persist($contribution);
		$em->flush();
		
		// Redirect to Paypal in order to make the payment
		return $this->redirect($payment->getApprovalLink());
	}
}
