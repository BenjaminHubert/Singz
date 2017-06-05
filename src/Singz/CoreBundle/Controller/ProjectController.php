<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Singz\CoreBundle\Entity\Project;
use Singz\CoreBundle\Form\ProjectType;

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
			// On redirige vers la page de visualisation de la publication nouvellement créée
			return $this->redirectToRoute('singz_user_bundle_homepage', array(
				'username' => $this->getUser()->getUsername()
			));
		}
		// Render the view
		return $this->render('SingzCoreBundle:Project:new.html.twig', array(
			'form' => $form->createView()
		));
	}
}
