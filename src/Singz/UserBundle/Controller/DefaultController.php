<?php

namespace Singz\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SingzUserBundle:Default:index.html.twig');
    }
}
