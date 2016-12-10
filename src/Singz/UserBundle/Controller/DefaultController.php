<?php

namespace Singz\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('SingzUserBundle:User')->find($id);

        $publications = $em->getRepository('SingzSocialBundle:Publication')->findBy(array('user' => $user));

        // Get publications' threads, comments
        $threads = array();
        $allComments = array();

        foreach($publications as $pub){
            $id = $pub->getId();
            $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
            if (null === $thread) {
                $thread = $this->container->get('fos_comment.manager.thread')->createThread();
                $thread->setId($id);
                $thread->setPermalink($request->getUri());

                // Add the thread
                $this->container->get('fos_comment.manager.thread')->saveThread($thread);
            }

            $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

            $threads[$id] = $thread;
            $allComments[$id] = $comments;
        }

        return $this->render('SingzUserBundle:Default:index.html.twig', array(
                "publications" => $publications,
                "threads" => $threads,
                "comments" => $allComments,
                "user" => $user
            )
        );
    }
}
