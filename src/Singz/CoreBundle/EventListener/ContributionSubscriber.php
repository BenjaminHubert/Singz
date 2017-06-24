<?php

namespace Singz\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\CoreBundle\Entity\Contribution;
use Singz\CoreBundle\Entity\Project;
use Singz\SocialBundle\Entity\Notification;

class ContributionSubscriber implements EventSubscriber
{	
	public function postPersist(LifecycleEventArgs $args){
		$contribution = $args->getEntity();
		// only act on some "Contribution" entity
		if (!$contribution instanceof Contribution) {
			return;
		}
		// Entity Manager
		$em = $args->getEntityManager();
		// Get project
		$project = $contribution->getProject();
		### Send notification to the project owner regarding this contribution ###
		$notif = new Notification();
		$notif->setUserFrom($contribution->getContributer());
		$notif->setUserTo($project->getRequester());
		$notif->setPublication(null);
		$notif->setProject($project);
		$message = sprintf(Notification::NEW_CONTRIBUTION, $contribution->getContributer(), $project->getName(), $contribution->getAmount());
		$notif->setMessage($message);
		$em->persist($notif);
		### Increase the amount reached of the project ###
		$currentAmountReached = $project->getAmountReached();
		$project->setAmountReached($currentAmountReached + floatval($contribution->getAmount()));
		### Set the project state as STATE_DONE if the amount reached is enought ###
		$cagnotte = $em->getRepository('SingzAdminBundle:Setting')->findOneBy(array(
			'name' => 'Cagnotte'
		));
		if($project->getAmountReached() >= $cagnotte->getValue()){
			// update the state
			$project->setState(Project::STATE_DONE);
			### Send notification to the project owner if the contributions project has reached the total needed
			$notif = new Notification();
			$notif->setUserFrom($project->getRequester());
			$notif->setUserTo($project->getRequester());
			$notif->setPublication(null);
			$notif->setProject($project);
			$message = sprintf(Notification::PROJECT_DONE, $project->getName());
			$notif->setMessage($message);
			$em->persist($notif);
		}
		$em->persist($project);
		// Flush
		$em->flush();
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