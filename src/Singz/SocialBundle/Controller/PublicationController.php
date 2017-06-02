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
    	// suppression
    	$em->remove($publication);
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
    	$id = $request->request->get('idPublication');
    	if($id == null){
    		throw $this->createNotFoundException('Parameter missing');
    	}
    	//Get entity manager
    	$em = $this->getDoctrine()->getManager();    	
    	// Get publication
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
    	if($publication == null) {
    		throw $this->createNotFoundException('Publication inexistante');
    	}
    	// Get resingz
        $resingz = $em->getRepository('SingzSocialBundle:Publication')->getResingz($publication->getVideo());
    	// Get thread
    	$thread = $publication->getThread();    	
    	// Get comments
    	$comments = $thread->getVisibleComments();
    	// Create the forms
    	$mainForm = null;
    	$forms = [];
    	if($this->getUser()){
    		// First depth
	    	$comment = new Comment();
	    	$comment->setAuthor($this->getUser());
	    	$comment->setParent(null);
	    	$comment->setThread($publication->getThread());
	    	$mainForm = $this
	    		->createForm(CommentType::class, $comment, array(
	    			'action' => $this->generateUrl('singz_social_bundle_new_comment')
	    		))
	    		->createView();
	    		
	    	// Second depth
	    	foreach($comments as $comment){
	    		$c = new Comment();
	    		$c->setAuthor($this->getUser());
	    		$c->setParent($comment);
	    		$c->setThread($publication->getThread());
	    		$forms[$comment->getId()] = $this
		    		->createForm(CommentType::class, $c, array(
		    			'action' => $this->generateUrl('singz_social_bundle_new_comment')
		    		))
		    		->createView();
	    	}
    	}
    	// Render the view
    	return $this->render('SingzSocialBundle::extra.html.twig', array(
    		'publication' => $publication,
            'resingz' => $resingz,
    		'comments' => $comments,
    		'thread' => $thread,
    		'main_form' => $mainForm,
    		'forms' => $forms,
    	));
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     */
    public function newCommentAction(Request $request){
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
    	$comment = $form->getData();
    	$em->persist($comment);
    	$em->flush();
    	return new Response(Response::HTTP_OK);
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editCommentAction(Request $request, $idComment, $state){
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
    		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'You do not have rights to access');
    	}
    	if($state == Comment::STATE_DELETED && ($comment->getAuthor() != $this->getUser() || $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') === false)){
    		throw $this->createAccessDeniedException('You do not have rights to access');
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
    public function reportCommentAction(Request $request, $idComment){
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
        if($owner->getIsPrivate() === true){
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à resingzer cette publication");
        }

        $newpub = new Publication();
        $newpub->setOwner($publication->getOwner());
        $newpub->setVideo($publication->getVideo());
        $newpub->setDescription($publication->getDescription());
        $newpub->setIsResingz(true);

        $em->persist($newpub);
        $em->flush();

        return new Response(null, Response::HTTP_OK);
    }
    
}
