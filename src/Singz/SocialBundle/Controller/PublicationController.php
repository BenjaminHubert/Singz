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

class PublicationController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('singz_social_bundle_publication_list');
    }

    public function newAction()
    {
        return $this->render('SingzSocialBundle:Publication:new.html.twig', array(
            // ...
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
