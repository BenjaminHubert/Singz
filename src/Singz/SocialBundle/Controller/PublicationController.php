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
    			//on définit alors l'user comme le current user
    			$publication->setUser($this->getUser());
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
    	// Get thread
    	$thread = $publication->getThread();    	
    	// Get comments
    	$comments = $thread->getComments();
    	// Render the view
    	return $this->render('SingzSocialBundle::extra.html.twig', array(
    		'publication' => $publication,
    		'comments' => $comments,
    		'thread' => $thread
    	));
    }
}
