<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Follow;
use Singz\SocialBundle\Entity\Notification;

class FollowSubscriber implements EventSubscriber
{    
    public function postPersist(LifecycleEventArgs $args)
    {
    	$follow = $args->getEntity();
    	// only act on some "Follow" entity
    	if (!$follow instanceof Follow) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	//create a notification
    		$notif = new Notification();
    		$notif->setUserFrom($follow->getFollower());
    		$notif->setUserTo($follow->getLeader());
    		$notif->setPublication(null);
    		$message = sprintf(Notification::NEW_FOLLOWER, $follow->getFollower()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);
    }
	
	public function getSubscribedEvents() {
		return array(
			'postPersist',
		);
	}

}