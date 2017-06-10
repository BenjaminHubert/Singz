<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Singz\CoreBundle\Entity\Project;
use Singz\CoreBundle\Form\ProjectType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
		// Render the view
		return $this->render('SingzCoreBundle:Project:show.html.twig', array(
			'project' => $project
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
}
