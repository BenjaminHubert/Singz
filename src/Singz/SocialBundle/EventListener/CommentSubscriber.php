<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Notification;
use Singz\SocialBundle\Entity\Comment;

class CommentSubscriber implements EventSubscriber
{    
    public function postPersist(LifecycleEventArgs $args)
    {
    	$comment = $args->getEntity();
    	// only act on some "Publication" entity
    	if (!$comment instanceof Comment) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	//create a notification if the commenter is not the publication owner
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->find($comment->getThread()->getId()); // publication.id = thread.id
    	if(!$publication){
    		return;
    	}
    	if($comment->getAuthor() != $publication->getUser()){
    		$notif = new Notification();
    		$notif->setUser($comment->getAuthor());
    		$notif->setPublication($publication);
    		$message = sprintf(Notification::NEW_COMMENT, $comment->getAuthor()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);
    	}
    }
	
	public function getSubscribedEvents() {
		return array(
			'postPersist',
		);
	}

}