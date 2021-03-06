<?php

namespace Singz\SocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
	/**
	 * @Security("has_role('ROLE_USER')")
	 */
    public function listAction()
    {
    	// Get entity manager
    	$em = $this->getDoctrine()->getManager();
    	// Get all user notifications
    	$notifications = $em->getRepository('SingzSocialBundle:Notification')->getNotificationsByUser($this->getUser());
    	// Define them as seen
    	foreach($notifications as $notification){
    		$notification->setIsSeen(true);
    		$em->persist($notification);
    	}
    	$em->flush();
        // Get all user pending followers
        $followers = $em->getRepository('SingzSocialBundle:Follow')->getPendingFollowers($this->getUser());

    	// Render the view
        return $this->render('SingzSocialBundle:Notification:list.html.twig', array(
            'notifications' => $notifications,
            'pending_followers' => $followers
        ));
    }

    /**
     * AJAX
     * Set a notification as readed
     * @param Request $request
     */
    public function notificationReadedAction(Request $request){
    	if($request->isXmlHttpRequest()) {
    		$idNotif = $request->request->get('idNotif');
    
    		// Get notification
    		$em = $this->getDoctrine()->getManager();
    		$notification = $em->getRepository('SingzSocialBundle:Notification')->find($idNotif);
    		if($notification == null) {
    			throw $this->createNotFoundException('Notification inexistante');
    		}
    		$notification->setIsRead(true);
    		$em->persist($notification);
    		$em->flush();
    		return new Response(null, 200);
    	}
    	return new Response('Error', 400);
    }
}
