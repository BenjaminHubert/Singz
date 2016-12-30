<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NotificationController extends Controller
{
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
    public function listAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$notifications = $em->getRepository('SingzSocialBundle:Notification')->findBy(array(
    		'user' => $this->getUser()			
    	));
        return $this->render('SingzSocialBundle:Notification:list.html.twig', array(
            'notifications' => $notifications
        ));
    }
}
