<?php

namespace Singz\VideoBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\VideoBundle\Entity\Video;

class VideoUpload
{
	private $ffprobe;
	
	public function __construct($ffprobe)
	{
		$this->ffprobe = $ffprobe;
	}
	
	public function prePersist(LifecycleEventArgs $args)
	{
		$video = $args->getEntity();		
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		
		// getting duration
		$path = $video->getFile()->getRealPath();
		dump($path);
		$duration = $this->ffprobe
			->format($path) // extracts file informations
			->get('duration');             // returns the duration property
		if($duration >= 60){
			$duration = gmdate('H:i:s', $duration);
		}else{
			$duration = gmdate('i:s', $duration);
		}
		// setting duration
		$video->setDuration($duration);
	}
}