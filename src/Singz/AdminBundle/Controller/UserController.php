<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function listAction()
    {
    	$users = $this->getDoctrine()->getManager()->getRepository('SingzUserBundle:User')->getUsersList();
        return $this->render('SingzAdminBundle:User:list.html.twig', array(
            'users' => $users
        ));
    }

    public function editAction(Request $request, $id)
    {
    	//get user
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('SingzUserBundle:User')->find($id);
    	if($user == null) {
    		throw $this->createNotFoundException('Utilisateur inexistant');
    	}
    	//create form
    	$editForm = $this->createForm('Singz\UserBundle\Form\UserType', $user);
    	$editForm->handleRequest($request);
    	
    	//if form submitted
    	if ($editForm->isSubmitted() && $editForm->isValid()) {
    		$em->persist($user);
    		$em->flush();
    		$this->addFlash('success', 'Utilisateur modifié');
    		return $this->redirectToRoute('singz_admin_user_edit', array('id' => $id));
    	}
    	
        return $this->render('SingzAdminBundle:User:edit.html.twig', array(
        	'user' => $user,
        	'edit_form' => $editForm->createView(),
        ));
    }

}
