<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction() {
        return $this->render('SingzCoreBundle:Core:comingsoon.html.twig');
    }

    public function browseAction(Request $request, $filter) {

        $user = $this->getUser();

        $offset = 0;
        $limit = 20;
        $now = new \DateTime();
        $interval = $now->sub(new \DateInterval("P30D"));

        $em = $this->getDoctrine()->getManager();

        // Get publications
        switch($filter) {
            case 'all':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseAll($offset, $limit, $interval, $user);
                break;
            case 'starz':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseStarz($offset, $limit, $interval);
                break;
            case 'singzer':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseSingzers($offset, $limit, $interval, $user);
                break;
            default:
                $publications = null;
                break;
        }

        // Get threads and comments
        $threads = array();
        $allComments = array();

        foreach ($publications as $pub) {
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

        return $this->render('SingzCoreBundle:Core:index.html.twig', array(
            "user" => $user,
            "publications" => $publications,
            "threads" => $threads,
            "comments" => $allComments
        ));
    }

    public function feedAction(Request $request){
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $publications = $em->getRepository('SingzSocialBundle:Publication')->getNewsFeed($user);


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

        return $this->render('SingzCoreBundle:Core:feed.html.twig', array(
            "user" => $user,
            "publications" => $publications,
            "threads" => $threads,
            "comments" => $allComments
        ));
    }
}
