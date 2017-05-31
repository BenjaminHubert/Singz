<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Publication;
use Singz\SocialBundle\Entity\Thread;
use Singz\SocialBundle\Repository\PublicationRepository;

class PublicationSubscriber implements EventSubscriber
{
    private $context;

    public function __construct($context) {
        $this->context = $context;
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
		if(!$publication->getThread()){
			throw new \Exception('Thread not found');
		}
		$publication->getThread()->setCommentable(false);
// 		$em->persist($thread);
	}

    public function prePersist(LifecycleEventArgs $args){
        $publication = $args->getEntity();
        // only act on some "Publication" entity
        if (!$publication instanceof Publication) {
            return;
        }
        // Entity Manager
        if($publication->getUser() == null){
            $publication->setUser($this->context->getToken()->getUser());
        }
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
		if (null === $publication->getThread()) {
			$thread = new Thread();
			$thread->setPublication($publication);
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
            'prePersist',
			'postPersist',
		);
	}

}