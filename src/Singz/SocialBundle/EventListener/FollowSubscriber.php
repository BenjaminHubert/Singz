<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Follow;
use Singz\SocialBundle\Entity\Notification;
use Singz\AdminBundle\Repository\SettingRepository;
use Singz\AdminBundle\Entity\Setting;
use Singz\UserBundle\Entity\User;

class FollowSubscriber implements EventSubscriber
{
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
    	$follow = $args->getEntity();
    	// only act on some "Follow" entity
    	if (!$follow instanceof Follow) {
    		return;
    	}
    	// get the entity manager
    	$em = $args->getEntityManager();
    	//create a notification
    		$notif = new Notification();
    		$notif->setUserFrom($follow->getFollower());
    		$notif->setUserTo($follow->getLeader());
    		$notif->setPublication(null);
    		$message = sprintf(($follow->getLeader()->getIsPrivate()?Notification::NEW_FOLLOWER_PRIVATE:Notification::NEW_FOLLOWER), $follow->getFollower()->getUsername());
    		$notif->setMessage($message);
    		$em->persist($notif);
    		$em->flush($notif);

        // check if Settings::Promotion is reached
            $setting = $em->getRepository('SingzAdminBundle:Setting')->getSettingByName('Promotion');
            if($setting != null) {
                $leader = $follow->getLeader();
                $roleService = $this->container->get('singz.user.service.role');
                if(!$roleService->isGranted(User::ROLE_STARZ, $leader)) {
                    $follows = $em->getRepository('SingzSocialBundle:Follow')->getFollowers($leader);
                    if(count($follows) >= $setting->getValue()) {
                        $leader->removeRole(User::ROLE_SINGZER);
                        $leader->addRole(User::ROLE_STARZ);
                        $em->persist($leader);
                        $em->flush($leader);

                        // create new notification
                        $notif = new Notification();
                        $notif->setUserFrom($follow->getLeader());
                        $notif->setUserTo($follow->getLeader());
                        $notif->setPublication(null);
                        $message = sprintf(Notification::PROMOTE, $follow->getLeader()->getUsername());
                        $notif->setMessage($message);
                        $em->persist($notif);
                        $em->flush($notif);
                    }
                }
            }

    }

    public function postUpdate(LifecycleEventArgs $args){
        $follow = $args->getEntity();
        // only act on some "Follow" entity
        if (!$follow instanceof Follow) {
            return;
        }
        // get the entity manager
        $em = $args->getEntityManager();
        // edit old notification
        $oldNotif = $em->getRepository('SingzSocialBundle:Notification')->findOneBy(array('userFrom'=>$follow->getFollower(), 'userTo'=>$follow->getLeader()));
        $message = sprintf(Notification::NEW_FOLLOWER, $follow->getFollower()->getUsername());
        $oldNotif->setMessage($message);
        $oldNotif->setIsRead(true);
        $em->persist($oldNotif);
        $em->flush($oldNotif);
        //create a notification
        $notif = new Notification();
        $notif->setUserFrom($follow->getLeader());
        $notif->setUserTo($follow->getFollower());
        $notif->setPublication(null);
        $message = sprintf(Notification::NEW_FOLLOW_ACCEPTED, $follow->getLeader()->getUsername());
        $notif->setMessage($message);
        $em->persist($notif);
        $em->flush($notif);
    }

    public function postRemove(LifecycleEventArgs $args){
        $follow = $args->getEntity();
        // only act on some "Follow" entity
        if (!$follow instanceof Follow) {
            return;
        }
        // get the entity manager
        $em = $args->getEntityManager();

        // check if Settings::Promotion is reached
        $setting = $em->getRepository('SingzAdminBundle:Setting')->getSettingByName('Promotion');
        if($setting != null) {
            $leader = $follow->getLeader();
            $roleService = $this->container->get('singz.user.service.role');
            if($roleService->isGranted(User::ROLE_STARZ, $leader)) {
                $follows = $em->getRepository('SingzSocialBundle:Follow')->getFollowers($leader);
                if(count($follows) < $setting->getValue()) {
                    $leader->removeRole(User::ROLE_STARZ);
                    $leader->addRole(User::ROLE_SINGZER);
                    $em->persist($leader);
                    $em->flush($leader);
                }
            }
        }

        //delete notification to avoid doubloons
        $notifs = $em->getRepository('SingzSocialBundle:Notification')->findBy(array('userFrom'=>$follow->getFollower(), 'userTo'=>$follow->getLeader()));
        foreach($notifs as $notif) {
            $em->remove($notif);
        }
        $em->flush();
    }
	
	public function getSubscribedEvents() {
		return array(
			'postPersist',
            'postUpdate',
            'postRemove'
		);
	}

}