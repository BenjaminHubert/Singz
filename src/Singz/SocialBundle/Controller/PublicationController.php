<?php

namespace Singz\SocialBundle\Controller;

use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\SingzSocialBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublicationController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('singz_social_bundle_publication_list');
    }

    public function addAction()
    {
        return $this->render('SingzSocialBundle:Publication:add.html.twig', array(
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

    public function showAction($id)
    {
        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication');
        $publication = $repository->find($id);

        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Love');
        $loves = $repository->findBy(array('publication' => $id));

        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->render('SingzSocialBundle:Publication:show.html.twig', array(
            'publication' => $publication,
            'loves' => $loves,
            'thread' => $thread,
            'comments' => $comments
        ));
    }

}
