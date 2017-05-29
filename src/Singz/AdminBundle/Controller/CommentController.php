<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
    public function indexAction()
    {
        return $this->redirectToRoute('singz_admin_comment_list');
    }

    public function listAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$comments = $em->getRepository('SingzSocialBundle:Comment')->findAllCommentsInfo();
		$reported = $em->getRepository('SingzSocialBundle:Report')->findAll();
    	return $this->render('SingzAdminBundle:Comment:list.html.twig', array(
            'comments' => $comments,
    		'reported' => $reported
        ));
    }
}
