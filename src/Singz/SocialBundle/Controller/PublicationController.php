<?php

namespace Singz\SocialBundle\Controller;

use Singz\SocialBundle\Entity\Love;
use Singz\SocialBundle\Entity\Notification;
use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\SingzSocialBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Singz\SocialBundle\Form\PublicationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PublicationController extends Controller
{
    public function indexAction(Request $request)
    {
    	throw $this->createNotFoundException();
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction(Request $request)
    {
    	//on crée notre publication 
    	$publication = new Publication();
    	
    	// on récupère le formulaire
    	$form = $this->createForm(PublicationType::class, $publication);
    	    	
    	// si le formulaire est soumis
    	if($request->isMethod('POST')){
    		//on met dans notre objet $publication les valeurs du formulaire
    		$form->handleRequest($request);
    		
    		// on vérifie la validation du formulaire
    		if($form->isValid()){
    			//on définit alors l'user comme le current user
    			$publication->setUser($this->getUser());
    			//on insère en base de données
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($publication);
    			$em->flush();
    			
    			//on affiche un message
    			$request->getSession()->getFlashBag()->add('success', 'Publication bien enregistrée.');
    			
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
    	if($publication->getUser() != $this->getUser()){
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
    			//on met à jour
    			$publication->setLastEdit(new \DateTime());
    			$em->flush();
    			 
    			//on affiche un message
    			$request->getSession()->getFlashBag()->add('success', 'Publication bien enregistrée.');
    			 
    			// On redirige vers la page de visualisation de la publication nouvellement créée
    			return $this->redirectToRoute('singz_social_bundle_publication_show', array('id' => $publication->getId()));
    		}
    	}
    	
    	return $this->render('SingzSocialBundle:Publication:edit.html.twig', array(
    		'form' => $form->createView()
    	));
    }
   	
    public function showAction(Request $request, $id)
    {
        // Get publication
        $publication = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
        if($publication == null) {
            throw $this->createNotFoundException('Publication inexistante');
        }
        $user = $publication->getUser();
        // Get thread
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        // Get comments
        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->render('SingzSocialBundle:Publication:show.html.twig', array(
            'user' => $user,
            'publication' => $publication,
            'thread' => $thread,
            'comments' => $comments
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
                $love->setDate(new \DateTime());

                $em->persist($love);

                // New notification
                if($user != $pub->getUser()) {
                    $notif = new Notification();
                    $notif->setUser($user);
                    $notif->setPublication($pub);
                    $notif->setDate(new \DateTime());
                    $notif->setMessage($user->getUsername()." love votre publication !");

                    $em->persist($notif);
                }
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
    	if($request->isXmlHttpRequest()) {
    		$id = $request->request->get('idPublication');
    		
    		// Get publication
    		$publication = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication')->getPublicationById($id);
    		if($publication == null) {
    			throw $this->createNotFoundException('Publication inexistante');
    		}
    		// Get thread
    		$thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
    		if (null === $thread) {
    			$thread = $this->container->get('fos_comment.manager.thread')->createThread();
    			$thread->setId($id);
    			$thread->setPermalink($request->getUri());
    		
    			// Add the thread
    			$this->container->get('fos_comment.manager.thread')->saveThread($thread);
    		}
    		
    		// Get comments
    		$comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
    		
    		return $this->render('SingzSocialBundle:Publication:extra.html.twig', array(
    			'publication' => $publication,
    			'comments' => $comments,
    			'thread' => $thread
    		));
    	}
    	return new Response('Error', 400);
    }

}
