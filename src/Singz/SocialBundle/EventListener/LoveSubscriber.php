<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Love;
use Singz\SocialBundle\Entity\Notification;

class LoveSubscriber implements EventSubscriber
{
    public function prePersist(LifecycleEventArgs $args)
    {
    	$love = $args->getEntity();
    	// only act on some "Love" entity
    	if (!$love instanceof Love) {
    		return;
    	}
    	// Increase the num_loves attribute in the Publication entity 
    	$love->getPublication()->increaseNumLoves();
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
    	$love = $args->getEntity();
    	// only act on some "Love" entity
    	if (!$love instanceof Love) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	//create a notification if the lover is not the publication owner
    	if($love->getUser() != $love->getPublication()->getUser()){
    		$notif = new Notification();
    		$notif->setUserFrom($love->getUser());
    		$notif->setUserTo($love->getPublication()->getUser());
    		$notif->setPublication($love->getPublication());
    		$message = sprintf(Notification::NEW_LOVE, $love->getUser()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);
    	}
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
    	$love = $args->getEntity();
    	// only act on some "Love" entity
    	if (!$love instanceof Love) {
    		return;
    	}
    	// Decrease the num_loves attribute in the Publication entity 
    	$love->getPublication()->decreaseNumLoves();
    }
	
	public function getSubscribedEvents() {
		return array(
			'prePersist',
			'postPersist',
			'preRemove',
		);
	}

}