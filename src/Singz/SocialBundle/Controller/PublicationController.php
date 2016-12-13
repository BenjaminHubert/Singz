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

class PublicationController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('singz_social_bundle_publication_list');
    }

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
    			$request->getSession()->getFlashBag()->add('notice', 'Publication bien enregistrée.');
    			
    			// On redirige vers la page de visualisation de la publication nouvellement créée
    			return $this->redirectToRoute('singz_social_bundle_publication_show', array('id' => $publication->getId()));
    		}
    	}
    	
        return $this->render('SingzSocialBundle:Publication:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function listAction(Request $request)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication');
        $publications = $repository->findAll();

        return $this->render('SingzSocialBundle:Publication:list.html.twig', array(
            'publications' => $publications
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

}
