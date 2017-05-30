<?php

namespace Singz\UserBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\UserBundle\Entity\User;
use Singz\UserBundle\Entity\Image;

class UserSubscriber implements EventSubscriber
{
    public function prePersist(LifecycleEventArgs $args)
    {
    	$user = $args->getEntity();
    	// only act on some "User" entity
    	if (!$user instanceof User) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	// Create new empty image
    	$image = new Image();
    	$image->setPath(null);
    	$em->persist($image);
    	$em->flush($image);
    	// Set image
    	$user->setImage($image);
    }
	
	public function getSubscribedEvents() {
		return array(
			'prePersist',
		);
	}

}