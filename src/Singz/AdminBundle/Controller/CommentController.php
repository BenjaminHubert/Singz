<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function stateAction(Request $request, $id, $state){
    	$em = $this->getDoctrine()->getManager();
    	$comment = $em->getRepository('Singz\SocialBundle\Entity\Comment')->find($id);
    	if($comment == null){
    		throw $this->createNotFoundException('Commentaire inexistant.');
    	}
    	
    	$states = [0, 1]; // states available
    	if(!in_array($comment->getState(), $states)){
    		throw $this->createNotFoundException('Statut inconnu.');
    	}
    	
    	//if confirmation done
    	if($request->query->get('confirmed') !== null && $request->query->get('confirmed') == true){
    		$comment->setState((int)$state);
    		$em->persist($comment);
    		$em->flush();
    		$request->getSession()->getFlashBag()->add('success', 'Comment updated');
    		return $this->redirectToRoute('singz_admin_comment_show', ['id' => $id]);
    	}else{
	    	return $this->render('SingzAdminBundle:Comment:state.html.twig', array(
	    		'comment' => $comment,
	    		'nextState' => $state
	    	));
    	}
    }
}
