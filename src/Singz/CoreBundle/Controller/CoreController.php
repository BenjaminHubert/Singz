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

        $folRepo = $em->getRepository('SingzSocialBundle:Follow');
        $pubRepo = $em->getRepository('SingzSocialBundle:Publication');
        $lovRepo = $em->getRepository('SingzSocialBundle:Love');

        ///// TEMPO :
        $user = $em->getRepository('SingzUserBundle:User')->find(54);
        /////////////

        // Get user's subscriptions
        $query = $folRepo->createQueryBuilder('f')
            ->where('f.follower = :follower AND f.isPending = :pending')
            ->setParameter('follower', $user)
            ->setParameter('pending', false)
            ->getQuery();
        $follows = $query->getResult();

        $leaders = array();
        foreach ($follows as $f) {
            array_push($leaders, $f->getLeader());
        }

        // Get subscriptions' publications
        $query = $pubRepo->createQueryBuilder('p')
            ->where('p.user in (:follows)')
            ->setParameter('follows', $leaders)
            ->orderBy('p.date', 'DESC')
            ->getQuery();
        $publications = $query->getResult();

        // Get publications' threads, comments and loves
        $threads = array();
        $allcomments = array();
        $loves = array();

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
            $love = $lovRepo->findBy(array('publication' => $id));

            $threads[$id] = $thread;
            $allcomments[$id] = $comments;
            $loves[$id] = $love;
        }

        return $this->render('SingzCoreBundle:Core:feed.html.twig', array(
            "publications" => $publications,
            "threads" => $threads,
            "comments" => $allcomments,
            "loves" => $loves
        ));
    }
}
