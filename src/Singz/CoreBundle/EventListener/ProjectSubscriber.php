<?php

namespace Singz\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\CoreBundle\Entity\Project;
use Singz\SocialBundle\Entity\Notification;

class ProjectSubscriber implements EventSubscriber
{	
	public function postPersist(LifecycleEventArgs $args){
		$project = $args->getEntity();
		// only act on some "Project" entity
		if (!$project instanceof Project) {
			return;
		}
		// Entity Manager
		$em = $args->getEntityManager();
		// Get project owner followers
		$followers = $project->getRequester()->getLeaders();
		// Send notification to followers
		foreach($followers as $follower){
			$notif = new Notification();
			$notif->setUserFrom($project->getRequester());
			$notif->setUserTo($follower->getFollower());
			$notif->setPublication(null);
			$notif->setProject($project);
			$message = sprintf(Notification::NEW_PROJECT, $project->getRequester()->getUsername(), $project->getName());
			$notif->setMessage($message);
			$em->persist($notif);
			$em->flush($notif);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'postPersist',
		);
	}

}