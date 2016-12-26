<?php

namespace Singz\VideoBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\VideoBundle\Entity\Video;

class VideoUploadSubscriber implements EventSubscriber
{
	private $ffprobe;
	private $ffmpeg;
	
	public function __construct($ffprobe, $ffmpeg)
	{
		$this->ffprobe = $ffprobe;
		$this->ffmpeg = $ffmpeg;
	}
	
	public function prePersist(LifecycleEventArgs $args)
	{
		$video = $args->getEntity();
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		
		// getting the video temporary path
		$path = $video->getFile()->getRealPath();
		
		// getting duration
		dump($path);
		$durationTime = $this->ffprobe
			->format($path) // extracts file informations
			->get('duration'); // returns the duration property
		// setting duration
		$video->setDuration((int)$durationTime);
	}
	
	public function postPersist(LifecycleEventArgs $args){
		$video = $args->getEntity();
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		// getting the video path
		$path = $video->getUploadRootDir().'/'.$video->getId().'.'.$video->getExtension();
		// getting a preview image at the middle of the video
		$file = $this->ffmpeg->open($path);
		$time = (int)($video->getDuration()/2);
		$frame = $file->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($time));
		$uploadDir = $video->getUploadRootDir().'/'.$video->getId().'.jpg';
		$frame->save($uploadDir);
	}
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'prePersist',
			'postPersist',
		);
	}

}