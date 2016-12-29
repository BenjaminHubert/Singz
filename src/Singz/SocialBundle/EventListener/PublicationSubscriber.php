<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Publication;

class PublicationSubscriber implements EventSubscriber
{
	public function preUpdate(LifecycleEventArgs $args){
		$publication = $args->getEntity();
		// only act on some "Publication" entity
		if (!$publication instanceof Publication) {
			return;
		}
		
		/* Update last edit */
		$publication->setLastEdit(new \DateTime());
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'preUpdate',
		);
	}

}