<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\Entity\Thread;

class PublicationSubscriber implements EventSubscriber
{
	private $container;
	
	public function __construct($container){
		$this->container = $container;
	}
	
	public function preUpdate(LifecycleEventArgs $args){
		$publication = $args->getEntity();
		// only act on some "Publication" entity
		if (!$publication instanceof Publication) {
			return;
		}
		
		/* Update last edit */
		$publication->setLastEdit(new \DateTime());
	}
	
	public function preRemove(LifecycleEventArgs $args){
		$publication = $args->getEntity();
		// only act on some "Publication" entity
		if (!$publication instanceof Publication) {
			return;
		}
		// Entity Manager
		$em = $args->getEntityManager();
		// Disable its thread
		$thread = $this->container->get('fos_comment.manager.thread')->findThreadById($publication->getId());
		$thread->setCommentable(false);
		$em->persist($thread);
	}
	
	public function postPersist(LifecycleEventArgs $args){
		$publication = $args->getEntity();
		// only act on some "Publication" entity
		if (!$publication instanceof Publication) {
			return;
		}
		// Entity Manager
		$em = $args->getEntityManager();
		// Creating the publication thread
		$thread = $this->container->get('fos_comment.manager.thread')->findThreadById($publication->getId());
		if (null === $thread) {
			$thread = new Thread();
			$thread->setId($publication->getId());
			$thread->setPermalink('');
			$em->persist($thread);
			$em->flush($thread);
		}
		
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'preUpdate',
			'preRemove',
			'postPersist',
		);
	}

}