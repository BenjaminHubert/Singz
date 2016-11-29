<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('SingzAdminBundle:Admin:dashboard.html.twig', array(
            // ...
        ));
    }

    public function indexAction()
    {
    	return $this->redirectToRoute('singz_admin_dashboard');
    }

}
