<?php

namespace Singz\SocialBundle\Controller;

use Singz\SocialBundle\Entity\Love;
use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\SingzSocialBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Singz\SocialBundle\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Singz\SocialBundle\Form\CommentType;
use Singz\SocialBundle\Entity\Comment;
use Singz\SocialBundle\Entity\Report;

class PublicationController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
    	//on crée notre publication 
    	$publication = new Publication();
    	
    	// on récupère le formulaire
    	$form = $this->createForm(PublicationType::class, $publication, array(
		    'action' => $this->generateUrl('singz_social_bundle_publication_new'),
		));
    	    	
    	// si le formulaire est soumis
    	if($request->isMethod('POST')){
    		//on met dans notre objet $publication les valeurs du formulaire
    		$form->handleRequest($request);
    		
    		// on vérifie la validation du formulaire
    		if($form->isValid()){
    			//on définit alors l'user et l'owner comme le current user
    			$publication->setOwner($this->getUser());
    			//on insère en base de données
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($publication);
    			$em->flush();
    			
    			//on affiche un message
//     			$this->addFlash('success', 'Publication bien enregistrée.');
    			
    			// On redirige vers la page de visualisation de la publication nouvellement créée
    			return $this->redirectToRoute('singz_social_bundle_publication_show', array('id' => $publication->getId()));
    		}
    	}

        return $this->render('SingzSocialBundle:Publication:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, $id){
    	// Get publication
    	$em = $this->getDoctrine()->getManager();
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
    	if($publication == null) {
    		throw $this->createNotFoundException('Publication inexistante');
    	}
    	// Check if the user is the publication owner
    	if(!$this->isGranted('ROLE_ADMIN') && $publication->getUser() != $this->getUser()){
    		throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à modifier cette publication");
    	}
    	
    	// on récupère le formulaire
    	$form = $this->createForm(PublicationType::class, $publication);
    	$form->remove('video');
    	
    	// si le formulaire est soumis
    	if($request->isMethod('POST')){
    		//on met dans notre objet $publication les valeurs du formulaire
    		$form->handleRequest($request);
    	
    		// on vérifie la validation du formulaire
    		if($form->isValid()){
    			// update
    			$em->flush();
    			 
    			//on affiche un message
    			$this->addFlash('success', 'Publication bien enregistrée.');
    			 
    			// On redirige vers la page de visualisation de la publication nouvellement créée
    			return $this->redirectToRoute('singz_social_bundle_publication_show', array('id' => $publication->getId()));
    		}
    	}
    	
    	return $this->render('SingzSocialBundle:Publication:edit.html.twig', array(
    		'form' => $form->createView()
    	));
    }
   	
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function deleteAction(Request $request, $id){
    	// Get publication
    	$em = $this->getDoctrine()->getManager();
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
    	if($publication == null) {
    		throw $this->createNotFoundException('Publication inexistante');
    	}
    	// Check if the user is the publication owner
    	if(!$this->isGranted('ROLE_ADMIN') && $publication->getUser() != $this->getUser()){
    		throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à supprimer cette publication");
    	}
    	// update
    	$publication->setState(Publication::STATE_DELETED);
    	$em->flush();
    	//on affiche un message
    	$this->addFlash('success', 'Publication supprimée avec succès.');
    	// On redirige vers la page de visualisation de la publication nouvellement créée
    	return $this->redirectToRoute('singz_feed');
    }
    
    public function showAction(Request $request, $id)
    {
        // Get publication
        $publication = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
        if($publication == null) {
            throw $this->createNotFoundException('Publication inexistante');
        }

        return $this->render('SingzSocialBundle:Publication:show.html.twig', array(
            'publication' => $publication,
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function loveAction(Request $request){

        if($request->isXmlHttpRequest()) {

            $idUser = $request->request->get('idUser');
            $idPub = $request->request->get('idPub');

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('SingzSocialBundle:Love');
            $love = $repository->findBy(array('user' => $idUser, 'publication' => $idPub));

            if(empty($love)) {
                $didLove = false;
                $user = $em->getRepository('SingzUserBundle:User')->find($idUser);
                $pub = $em->getRepository('SingzSocialBundle:Publication')->find($idPub);

                // New love
                $love = new Love();
                $love->setUser($user);
                $love->setPublication($pub);
                $em->persist($love);
            } else {
                $didLove = true;
                $em->remove(array_pop($love));
            }
            $em->flush();

            $loves = $repository->findBy(array('publication' => $idPub));

            return new JsonResponse(array('loves' => $loves, 'didLove' => $didLove));
        }

        return new Response('Error', 400);
    }
    
    /**
     * Retrieve publication description, date and comments through Ajax
     * @param Request $request
     */
    public function getPublicationExtraAction(Request $request){
    	// Check if AJAX request
    	if(!$request->isXmlHttpRequest()) {
    		return new Response('Must be a XML HTTP request', 400);
    	}
    	//Get ID publication
    	$id = $request->query->get('idPublication');
    	if($id == null){
    		return new JsonResponse(array(
    			'error' => 'Parameter missing'
    		), Response::HTTP_NOT_FOUND);
    	}
    	//Get entity manager
    	$em = $this->getDoctrine()->getManager();    	
    	// Get publication
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
    	if($publication == null) {
    		return new JsonResponse(array(
    			'error' => 'Publication inexistante'
    		), Response::HTTP_NOT_FOUND);
    	}
    	// Get resingz
        $resingz = $em->getRepository('SingzSocialBundle:Publication')->getResingz($publication->getVideo());
        // Check if the current user has resingz
        $hasResingz = false;
        if(($currentUser = $this->getUser())){
        	$q = $em->getRepository('SingzSocialBundle:Publication')->findBy(array(
        		'user' => $currentUser,
        		'video' => $publication->getVideo(),
        		'isResingz' => true,
        		'state' => Publication::STATE_VISIBLE
        	));
        	if(!empty($q)){
        		$hasResingz = true;
        	}
        }
    	// Get thread
    	$thread = $publication->getThread();
    	// Get comments
    	$comments = $thread->getComments(array(Comment::STATE_VISIBLE));
    	// Create the forms
    	$mainForm = null;
    	$forms = [];
    	if($currentUser){
    		// First depth
	    	$comment = new Comment();
	    	$comment->setAuthor($currentUser);
	    	$comment->setParent(null);
	    	$comment->setThread($publication->getThread());
	    	$mainForm = $this
	    		->createForm(CommentType::class, $comment, array(
	    			'action' => $this->generateUrl('singz_social_bundle_comment_new')
	    		))
	    		->createView();
	    		
	    	// Second depth
	    	foreach($comments as $comment){
	    		$c = new Comment();
	    		$c->setAuthor($currentUser);
	    		$c->setParent($comment);
	    		$c->setThread($publication->getThread());
	    		$forms[$comment->getId()] = $this
		    		->createForm(CommentType::class, $c, array(
		    			'action' => $this->generateUrl('singz_social_bundle_comment_new')
		    		))
		    		->createView();
	    	}
    	}
    	// Render the view
    	$html = $this->renderView('SingzSocialBundle::extra.html.twig', array(
    		'publication' => $publication,
            'resingz' => $resingz,
    		'comments' => $comments,
    		'thread' => $thread,
    		'main_form' => $mainForm,
    		'forms' => $forms,
    		'hasResingz' => $hasResingz
    	));
    	
    	return new JsonResponse(array(
    		'html' => $html
    	), Response::HTTP_OK);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function resingzAction(Request $request, $id){
        // Check if AJAX request
        if(!$request->isXmlHttpRequest()) {
            return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
        }
        // Get publication
        $em = $this->getDoctrine()->getManager();
        $publication = $em->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
        if($publication == null) {
            throw $this->createNotFoundException('Publication inexistante');
        }
        // Check if the owner is private
        $owner = $publication->getOwner();
        if($owner->getIsPrivate() === true && $owner != $this->getUser()){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à resingzer cette publication");
        }
        // Check if the resingz already exists
        $hasResingz = false;
        $q = $em->getRepository('SingzSocialBundle:Publication')->findOneBy(array(
        	'user' => $this->getUser(),
        	'video' => $publication->getVideo(),
        	'isResingz' => true,
        	'state' => Publication::STATE_VISIBLE
        ));
        if(!empty($q)){
        	$hasResingz = true;
        }
        // Update the database
        if($hasResingz){
        	$q->setState(Publication::STATE_DELETED);
	        $em->persist($q);
        }else{
	        // Publication creation
	        $newpub = new Publication();
	        $newpub->setOwner($publication->getOwner());
	        $newpub->setVideo($publication->getVideo());
	        $newpub->setDescription($publication->getDescription());
	        $newpub->setIsResingz(true);
	        $em->persist($newpub);
        }
        $em->flush();
		// Return OK
        return new Response(null, Response::HTTP_OK);
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function reportAction(Request $request, $id){
    	// Check if AJAX request
    	if(!$request->isXmlHttpRequest()) {
    		return new Response('Must be an XML HTTP request', Response::HTTP_BAD_REQUEST);
    	}
    	// Get entity manager
    	$em = $this->getDoctrine()->getManager();
    	// Get publication
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->find($id);
    	if(!$publication){
    		return $this->createNotFoundException('La publication n\'existe pas');
    	}
    	// Create report
    	$report = new Report();
    	$report->setReporter($this->getUser());
    	$report->setPublication($publication);
    	$em->persist($report);
    	$em->flush();
    
    	return new Response();
    }
    
    public function getPublicationsAction(Request $request){
    	// Check if ajax
    	if(!$request->isXmlHttpRequest()){
    		return new JsonResponse('Bad request', Response::HTTP_BAD_REQUEST);
    	}
    	// Get params
    	$filter = $request->query->get('filter', 'all');
    	$offset = $request->query->get('offset', 0);
    	$limit  = $request->query->get('limit', PHP_INT_MAX);
    	$userId  = $request->query->get('userId', null);
    	$hashtag  = $request->query->get('hashtag', null);
    	// Check params
    	if(!is_numeric($offset) || (is_numeric($offset) && $offset < 0)){
    		return new JsonResponse('Unknown offset', Response::HTTP_BAD_REQUEST);
    	}
    	if(!is_numeric($limit) || (is_numeric($limit) && $limit < 0)){
    		return new JsonResponse('Unknown limit', Response::HTTP_BAD_REQUEST);
    	}
    	// Get publications
    	$publications = $this->getDoctrine()->getManager()
    		->getRepository('SingzSocialBundle:Publication')
    		->getPublications($this->getUser(), $filter, $offset, $limit, $userId, $hashtag);
    	
    	$html = $this->renderView('SingzSocialBundle:Publication:publications.html.twig', array(
    		'publications' => $publications
    	));
    		
    	return new JsonResponse(array(
    		'html' 	=> $html,
    		'filter' 		=> $filter,
    		'offset'		=> $offset,
    		'limit' 		=> $limit
    	));
    }
}
