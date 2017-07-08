<?php

namespace Singz\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function indexAction($name)
    {
        return $this->redirectToRoute('singz_admin_project_list');
    }

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $allProjects = $em->getRepository('SingzCoreBundle:Project')->findAllProjectsInfo();

        return $this->render('SingzAdminBundle:Project:list.html.twig', array(
            'projects' => $allProjects
        ));
    }
}
