<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Report;

class ReportSubscriber implements EventSubscriber
{    
    public function prePersist(LifecycleEventArgs $args)
    {
    	$report = $args->getEntity();
    	// only act on some "Report" entity
    	if (!$report instanceof Report) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	// check if the report already exist
    	$isExist = $em->getRepository('SingzSocialBundle:Report')->findOneBy(array(
    		'reporter' => $report->getReporter(),
    		'comment' => $report->getComment(),
    		'publication' => $report->getPublication()
    	));
    	if($isExist){
    		$em->remove($isExist);
    		$em->flush();
    	}
    }
	
	public function getSubscribedEvents() {
		return array(
			'prePersist',
		);
	}

}