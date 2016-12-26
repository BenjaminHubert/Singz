<?php

namespace Singz\VideoBundle\DoctrineListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\VideoBundle\Entity\Video;

class VideoUpload
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
		if($durationTime >= 60){
			$duration = gmdate('H:i:s', $durationTime);
		}else{
			$duration = gmdate('i:s', $durationTime);
		}
		// setting duration
		$video->setDuration($duration);
		
		// getting a preview image at the middle of the video
		$file = $this->ffmpeg->open($path);
		$time = (int)($durationTime/2);
		$frame = $file->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($time));
		$uploadDir = $video->getUploadRootDir().'/'.$video->getId().'.jpg';
		$frame->save($uploadDir);
	}
}