<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function listAction()
    {
    	$users = $this->getDoctrine()->getManager()->getRepository('SingzUserBundle:User')->getUsersList();
        return $this->render('SingzAdminBundle:User:list.html.twig', array(
            'users' => $users
        ));
    }

    public function showAction()
    {
        return $this->render('SingzAdminBundle:User:show.html.twig', array(
            // ...
        ));
    }

    public function editAction()
    {
        return $this->render('SingzAdminBundle:User:edit.html.twig', array(
            // ...
        ));
    }

}
