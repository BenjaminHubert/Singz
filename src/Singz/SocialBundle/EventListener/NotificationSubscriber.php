<?php

namespace Singz\SocialBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\SocialBundle\Entity\Notification;
use Gos\Component\WebSocketClient\Wamp\Client;

class NotificationSubscriber implements EventSubscriber
{   
	private $webSocketHost;
	private $webSocketPort;
	
	public function __construct($webSocketHost, $webSocketPort){
		$this->webSocketHost = $webSocketHost;
		$this->webSocketPort = $webSocketPort;
	}
    public function postPersist(LifecycleEventArgs $args)
    {
    	$notification = $args->getEntity();
    	// only act on some "Notification" entity
    	if (!$notification instanceof Notification) {
    		return;
    	}
    	// Push JS notification to the client if connected
    	try{
	    	$topic = 'notification_topic/'.$notification->getUserTo()->getId();
	    	$client = new Client($this->webSocketHost, $this->webSocketPort);
	    	$sessionId = $client->connect();
	    	$message = $notification->getMessage();
	    	$client->publish($topic, $message);
	    	//publish an event
	    	$client->event($topic, $message);
	    	$client->disconnect();
    	}catch(\Exception $e){
    		// no actions required
    	}
    	
    }
	
	public function getSubscribedEvents() {
		return array(
			'postPersist',
		);
	}

}