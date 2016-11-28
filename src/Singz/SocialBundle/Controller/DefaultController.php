<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SingzSocialBundle:Default:index.html.twig');
    }
}
