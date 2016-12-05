<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
