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
    	### CREATE NOTIFICATION ###
    	$publication = $em->getRepository('SingzSocialBundle:Publication')->find($comment->getThread()->getPublication()->getId());
    	if(!$publication){
    		return;
    	}
    	// Depth level 0
    	// Create a notification to the publication owner
    	if($comment->getAuthor() != $publication->getUser() && $comment->getParent() === NULL){
    		$notif = new Notification();
    		$notif->setUserFrom($comment->getAuthor());
    		$notif->setUserTo($publication->getUser());
    		$notif->setPublication($publication);
    		$message = sprintf(Notification::NEW_COMMENT, $comment->getAuthor()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);
    	}
    	//Depth level 1+
    	// Create a notification to the depth level 0 comment commenter
    	if($comment->getParent() != NULL && $comment->getAuthor() != $comment->getParent()->getAuthor()){
    		$notif = new Notification();
    		$notif->setUserFrom($comment->getAuthor());
    		$notif->setUserTo($comment->getParent()->getAuthor());
    		$notif->setPublication($publication);
    		$message = sprintf(Notification::REPLY_COMMENT, $comment->getAuthor()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);
    	}
    	
    	### UPDATE THREAD DATA ###
    	$thread = $comment->getThread();
    	//update $numComments
    	$thread->setNumComments($thread->getNumComments()+ 1);
    	//update $lastCommentAt
    	$thread->setLastCommentAt(new \DateTime());
    	$em->persist($thread);
    	$em->flush($thread);
    }
	
	public function getSubscribedEvents() {
		return array(
			'postPersist',
		);
	}

}