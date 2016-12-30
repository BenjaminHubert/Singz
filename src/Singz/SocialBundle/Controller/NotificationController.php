<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotificationController extends Controller
{
    public function listAction()
    {
        return $this->render('SingzSocialBundle:Notification:list.html.twig', array(
            
        ));
    }
}
