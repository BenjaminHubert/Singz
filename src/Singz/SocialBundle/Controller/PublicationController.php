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

}
