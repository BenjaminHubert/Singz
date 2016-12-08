<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Request;

class CoreController extends Controller
{
    public function indexAction() {
        return $this->render('SingzCoreBundle:Core:comingsoon.html.twig');
    }

    public function browseAction($filter) {
        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication');

        $nbPub = 20;

        $now = new \DateTime();
        $thirtyDaysAgo = $now->sub(new \DateInterval("P30D"));

        switch($filter) {
            case 'all':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    ->where('p.date > :tda ')
                    ->setParameter('tda', $thirtyDaysAgo)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults($nbPub)
                    ->getQuery();
                break;
            case 'singzer':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    ->where('p.date > :tda AND u.roles NOT LIKE :roles')
                    ->setParameter('roles', '%"ROLE_STARZ"%')
                    ->setParameter('tda', $thirtyDaysAgo)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults($nbPub)
                    ->getQuery();
                break;
            case 'starz':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    ->where('p.date > :tda AND u.roles LIKE :roles')
                    ->setParameter('roles', '%"ROLE_STARZ"%')
                    ->setParameter('tda', $thirtyDaysAgo)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults($nbPub)
                    ->getQuery();
                break;
        }

        $publications = $query->getResult();

        return $this->render('SingzCoreBundle:Core:index.html.twig', array(
            "publications" => $publications
        ));
    }

    public function feedAction(){
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        ///// TEMPO :
        $user = $em->getRepository('SingzUserBundle:User')->find(54);
        /////////////
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
            "publications" => $publications,
            "threads" => $threads,
            "comments" => $allComments
        ));
    }
}
