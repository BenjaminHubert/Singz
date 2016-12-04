<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function browseAction($filter) {
        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication');

        $nbPub = 5;

        $now = new \DateTime();
        $threedaysago = $now->sub(new \DateInterval("P3D"));

        switch($filter) {
            case 'all':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    //->where('p.date > :tda ')
                    //->setParameter('tda', $threedaysago)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults(5)
                    ->getQuery();
                break;
            case 'singzer':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    ->where('u.roles NOT LIKE :roles')
                    ->setParameter('roles', '%"ROLE_STARZ"%')
                    //->where('p.date > :tda ')
                    //->setParameter('tda', $threedaysago)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults(5)
                    ->getQuery();
                break;
            case 'starz':
                $query = $repository->createQueryBuilder('p')
                    ->select('p, COUNT(l.publication) as l_count, u.roles')
                    ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
                    ->leftjoin('SingzUserBundle:User', 'u', 'WITH', 'p.user = u.id')
                    ->where('u.roles LIKE :roles')
                    ->setParameter('roles', '%"ROLE_STARZ"%')
                    //->where('p.date > :tda ')
                    //->setParameter('tda', $threedaysago)
                    ->groupBy('p')
                    ->orderBy('l_count', 'DESC')
                    ->addOrderBy('p.date', 'DESC')
                    ->setMaxResults(5)
                    ->getQuery();
                break;
        }

        $publications = $query->getResult();

        dump($publications);

        return $this->render('SingzCoreBundle:Core:index.html.twig', array(
            "publications" => $publications
        ));
    }
}
