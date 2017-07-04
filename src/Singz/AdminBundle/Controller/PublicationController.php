<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PublicationController extends Controller
{
    public function indexAction($name)
    {
        return $this->redirectToRoute('singz_admin_comment_list');
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $allPublications = $em->getRepository('SingzSocialBundle:Publication')->findAllPublicationsInfo();
        return $this->render('SingzAdminBundle:Publication:list.html.twig', array(
            'publications' => $allPublications
        ));
    }
}
