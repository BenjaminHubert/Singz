<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Singz\SocialBundle\Entity\Comment;
use Singz\SocialBundle\Form\CommentType;
use Singz\SocialBundle\Entity\Report;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class CommentController extends Controller
{
	/**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     */
    public function newAction(Request $request){
    	// Check if AJAX request
    	if(!$request->isXmlHttpRequest()) {
    		return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
    	}
    	// Get entity manager
    	$em = $this->getDoctrine()->getManager();
    	// Create form
    	$comment = new Comment();
    	$form = $this->createForm(CommentType::class, $comment);
    	$form->handleRequest($request);
    	if(!$form->isSubmitted()){
    		return new Response('Form not submitted', Response::HTTP_BAD_REQUEST);
    	}
    	if(!$form->isValid()){
    		return new Response('Form not valid', Response::HTTP_BAD_REQUEST);
    	}
    	// Create the comment
    	$comment = $form->getData();
    	$em->persist($comment);
    	$em->flush();
    	// Get the html comment
    	$html = $this->renderView('SingzSocialBundle:Comment:comment.html.twig', array(
    		'comment' => $comment
    	));
    	return new JsonResponse(array(
    		'html' => $html,
    		'idComment' => $comment->getId()
    	));
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, $idComment, $state){
    	// Check if AJAX request
    	if(!$request->isXmlHttpRequest()) {
    		return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
    	}
    	// Get entity manager
    	$em = $this->getDoctrine()->getManager();
    	// Get comment
    	$comment = $em->getRepository('SingzSocialBundle:Comment')->find($idComment);
    	if(!$comment){
    		return $this->createNotFoundException('Comment does not exist');
    	}
    	// Check if state exist
    	if($state != Comment::STATE_DELETED && $state != Comment::STATE_PENDING && $state != Comment::STATE_SPAM && $state != Comment::STATE_VISIBLE){
    		return new Response('Unknown comment state', Response::HTTP_BAD_REQUEST);
    	}
    	// Check the rights
    	if($state == Comment::STATE_PENDING || $state == Comment::STATE_SPAM || $state == Comment::STATE_VISIBLE){
    		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You must be an admin to access');
    	}
    	if($state == Comment::STATE_DELETED && $comment->getAuthor() != $this->getUser() && $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') != true){
    		throw $this->createAccessDeniedException('You must be an admin or the author');
    	}
    	//Update the state
    	$comment->setState($state);
    	$em->persist($comment);
    	$em->flush();
    	
    	return new Response();
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function reportAction(Request $request, $idComment){
    	// Check if AJAX request
    	if(!$request->isXmlHttpRequest()) {
    		return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
    	}
    	// Get entity manager
    	$em = $this->getDoctrine()->getManager();
    	// Get comment
    	$comment = $em->getRepository('SingzSocialBundle:Comment')->find($idComment);
    	if(!$comment){
    		return $this->createNotFoundException('Comment does not exist');
    	}
    	// Create report
    	$report = new Report();
    	$report->setReporter($this->getUser());
    	$report->setComment($comment);
    	$em->persist($report);
    	$em->flush();
    	 
    	return new Response();
    }
}
