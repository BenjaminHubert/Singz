<?php

namespace Singz\VideoBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Singz\VideoBundle\Entity\Video;
use FFMpeg\Format\Video\X264;

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
		//getting entity maanger
		$em = $args->getEntityManager();
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
		### CONVERTION VIDEO TO MP4###
		$oldPath = $video->getUploadRootDir().DIRECTORY_SEPARATOR.$video->getId().'.'.$video->getExtension();
		$copyPath = $video->getUploadRootDir().DIRECTORY_SEPARATOR.$video->getId().'.copy.'.$video->getExtension();
		$newPath = $video->getUploadRootDir().DIRECTORY_SEPARATOR.$video->getId().'.mp4';
		copy($oldPath, $copyPath);
		unlink($oldPath);
		$file = $this->ffmpeg->open($copyPath);
		$format = new X264('libmp3lame', 'libx264');
		$file->save($format, $newPath);
		unlink($copyPath);
		//define the new format
		$video->setExtension('mp4');
		$em->persist($video);
		$em->flush($video);
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
		// On sauvegarde temporairement le nom du fichier video, car il dépend de l'id
		$video->setTempFilename($video->getUploadRootDir().'/'.$video->getId().'.'.$video->getExtension());
		// On sauvegarde temporairement le nom du fichier preview, car il dépend de l'id
		$video->setTempFilenamePreview($video->getUploadRootDir().'/'.$video->getId().'.jpg');
	}
	
	public function postRemove(LifecycleEventArgs $args){
		$video = $args->getEntity();
		// only act on some "Video" entity
		if (!$video instanceof Video) {
			return;
		}
		// On supprime la vidéo
		if (file_exists($video->getTempFilename())) {
			// On supprime le fichier
			unlink($video->getTempFilename());
		}
		// On supprime la preview
		if (file_exists($video->getTempFilenamePreview())) {
			// On supprime le fichier
			unlink($video->getTempFilenamePreview());
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