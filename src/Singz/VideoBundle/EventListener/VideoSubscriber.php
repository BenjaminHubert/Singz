<?php

namespace Singz\VideoBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\VideoBundle\Entity\Video;

class VideoSubscriber implements EventSubscriber
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
		### SET EXTENSION ###
		//s'il n'y a pas de fichier, on ne fait pas d'upload
		if (null === $video->getFile()) {
			return;
		}
		// Le nom du fichier est son id, on doit juste stocker également son extension
		$video->setExtension($video->getFile()->guessExtension());
		
		### GET/SET DURATION ###
		// getting the video temporary path
		$path = $video->getFile()->getRealPath();
		// getting duration
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
		### UPLOAD VIDEO ###
		//s'il n'y a pas de fichier définit, on ne fait pas d'upload
		if (null === $video->getFile()) {
			return;
		}
		//si on avait un ancien fichier, on le supprime
		if (null !== $video->getTempFilename()) {
			if (file_exists($video->getTempFilename())) {
				unlink($video->getTempFilename());
			}
		}
		//on déplace le fichier dans notre dossier
		$video->getFile()->move(
				$video->getUploadRootDir(),
				$video->getId().'.'.$video->getExtension()
				);
		### CREATE PREVIEW IMAGE ###
		// getting the video path
		$path = $video->getUploadRootDir().DIRECTORY_SEPARATOR.$video->getId().'.'.$video->getExtension();
		// getting a preview image at the middle of the video
		$file = $this->ffmpeg->open($path);
		$time = (int)($video->getDuration()/2);
		$frame = $file->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($time));
		$uploadDir = $video->getUploadRootDir().DIRECTORY_SEPARATOR.$video->getId().'.jpg';
		$frame->save($uploadDir);
	}
	
	public function preRemove(LifecycleEventArgs $args){
		$video = $args->getEntity();
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		// On sauvegarde temporairement le nom du fichier, car il dépend de l'id
		$video->setTempFilename($video->getUploadRootDir().'/'.$video->getId().'.'.$video->getExtension());
	}
	
	public function postRemove(LifecycleEventArgs $args){
		$video = $args->getEntity();
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		// En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
		if (file_exists($video->getTempFilename())) {
			// On supprime le fichier
			unlink($video->getTempFilename());
		}
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents() {
		return array(
			'prePersist',
			'postPersist',
			'preRemove',
			'postRemove',
		);
	}

}