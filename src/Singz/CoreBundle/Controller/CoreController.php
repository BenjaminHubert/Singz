<?php

namespace Singz\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CoreController extends Controller
{
	
    public function browseAction(Request $request, $filter) {

        $user = $this->getUser();

        $offset = 0;
        $limit = 20;
        $now = new \DateTime();
        $interval = $now->sub(new \DateInterval("P30D"));

        $em = $this->getDoctrine()->getManager();

        // Get publications
        switch($filter) {
            case 'all':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseAll($offset, $limit, $interval, $user);
                break;
            case 'starz':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseStarz($offset, $limit, $interval);
                break;
            case 'singzer':
                $publications = $em->getRepository('SingzSocialBundle:Publication')->getBrowseSingzers($offset, $limit, $interval, $user);
                break;
            default:
                $publications = null;
                break;
        }

        return $this->render('SingzCoreBundle:Core:browse.html.twig', array(
            "publications" => $publications
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function feedAction(Request $request){
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $publications = $em->getRepository('SingzSocialBundle:Publication')->getNewsFeed($user);
        return $this->render('SingzCoreBundle:Core:feed.html.twig', array(
            "publications" => $publications
        ));
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
        $user = $this->getUser();
        $hashtag = $request->get('k');
        if($hashtag == null) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();
        $publications = $em->getRepository('SingzSocialBundle:Publication')->getPublicationByHashtag($user, $hashtag);
        $projects = $em->getRepository('SingzCoreBundle:Project')->getProjectByHashtag($hashtag);
        return $this->render('SingzCoreBundle:Core:hashtag.html.twig', array(
            "publications" => $publications,
            "projects" => $projects,
            "hashtag" => $hashtag
        ));
    }
}
