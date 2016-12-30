<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Love;

class LoveSubscriber implements EventSubscriber
{	
    public function prePersist(LifecycleEventArgs $args)
    {
    	$love = $args->getEntity();
    	// only act on some "Publication" entity
    	if (!$love instanceof Love) {
    		return;
    	}
    	// Increase the num_loves attribute in the Publication entity 
    	$love->getPublication()->increaseNumLoves();
    }
    
    public function preRemove(LifecycleEventArgs $args)
    {
    	$love = $args->getEntity();
    	// only act on some "Publication" entity
    	if (!$love instanceof Love) {
    		return;
    	}
    	// Decrease the num_loves attribute in the Publication entity 
    	$love->getPublication()->decreaseNumLoves();
    }
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'prePersist',
			'preRemove'
		);
	}

}