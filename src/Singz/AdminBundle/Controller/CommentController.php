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
    	$comments = $this->getDoctrine()->getManager()->getRepository('Singz\SocialBundle\Entity\Comment')->findAllCommentsInfo();

    	return $this->render('SingzAdminBundle:Comment:list.html.twig', array(
            'comments' => $comments
        ));
    }

    public function showAction($id)
    {
    	$comment = $this->getDoctrine()->getManager()->getRepository('Singz\SocialBundle\Entity\Comment')->find($id);
    	if($comment == null){
    		throw $this->createNotFoundException('Commentaire inexistant.');
    	}

    	return $this->render('SingzAdminBundle:Comment:show.html.twig', array(
            'comment' => $comment
        ));
    }
}
