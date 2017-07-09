<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CoreController extends Controller
{
	
    public function browseAction(Request $request, $filter) {
        // Render view
        return $this->render('SingzCoreBundle:Core:browse.html.twig');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function feedAction(Request $request){
        // Render view
        return $this->render('SingzCoreBundle:Core:feed.html.twig');
    }
    
    public function menuAction(){
    	if($this->getUser()){
    		$em = $this->getDoctrine()->getManager();
    		$notifications = $em->getRepository('SingzSocialBundle:Notification')->findBy(array(
    			'userTo' => $this->getUser(),
    			'isSeen' => false
    		));
    		$notificationsUnseen = count($notifications);
    	}else{
    		$notificationsUnseen = 0;
    	}
    	return $this->render('SingzCoreBundle::menu.html.twig', array(
    		'notificationsUnseen' => $notificationsUnseen
    	));
    }

    public function hashtagzAction(Request $request) {
        $hashtag = $request->get('k');
        if($hashtag == null) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $projects = $em->getRepository('SingzCoreBundle:Project')->getProjectByHashtag($hashtag);
        return $this->render('SingzCoreBundle:Core:hashtag.html.twig', array(
            'projects' => $projects,
            'hashtag' => $hashtag
        ));
    }
}
