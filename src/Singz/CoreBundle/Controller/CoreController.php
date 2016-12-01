<?php

namespace Singz\CoreBundle\Controller;

use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\SingzSocialBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoreController extends Controller
{
    public function indexAction(Request $request)
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('SingzSocialBundle:Publication');

        $now = new \DateTime();
        $threedaysago = $now->sub(new \DateInterval("P3D"));

        $query = $repository->createQueryBuilder('p')
            ->select('p, COUNT(l.publication) as l_count')
            ->leftjoin('SingzSocialBundle:Love', 'l', 'WITH', 'p.id = l.publication')
            //->where('p.date > :tda ')
            //->setParameter('tda', $threedaysago)
            ->groupBy('p')
            ->orderBy('l_count', 'DESC')
            ->addOrderBy('p.date', 'DESC')
            ->getQuery();

        $publications = $query->getResult();

        return $this->render('SingzCoreBundle:Core:index.html.twig', array(
            "publications" => $publications
        ));
    }

}
