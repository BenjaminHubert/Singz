<?php

namespace Singz\VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="Singz\VideoBundle\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Video
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Publication
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Publication", mappedBy="video")
     */
    private $publications;
    
    /**
     * @var UploadedFile
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {
	 *			"video/mpeg",
	 *			"video/mp4",
	 *			"video/quicktime",
	 *			"video/x-ms-wmv",
	 *			"video/x-msvideo",
	 *			"video/x-flv",
	 *			"video/webm"
     *     },
     *     mimeTypesMessage = "Le type de vidéo ({{ type }}) n'est pas correct. Les types autorisés sont uniquement des fichiers vidéos."
     * )
     */
    private $file;
    
    /**
     * @var string
     */
    private $tempFilename;
    
    /**
     * @var string
     *
     ** @ORM\Column(name="extension", type="string", length=255, nullable=true)
     */
    private $extension;
    
    /**
     * @var int
     *
     ** @ORM\Column(name="duration", type="integer", nullable=true)
     */
    private $duration;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the video path
     *
     * @return string
     */
    public function getVideoPath()
    {
        return $this->getUploadDir().'/'.$this->id.'.'.$this->extension;
    }

    /**
     * Get the video path
     *
     * @return string
     */
    public function getPreviewPath()
    {
        return $this->getUploadDir().'/'.$this->id.'.jpg';
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->publications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return Video
     */
    public function addPublication(\Singz\SocialBundle\Entity\Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     */
    public function removePublication(\Singz\SocialBundle\Entity\Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Video
     */
    public function setExtension($extension)
    {
    	$this->extension = $extension;
    
    	return $this;
    }
    
    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
    	return $this->extension;
    }
    
    /**
     * Get File
     *
     * @return UploadedFile
     */
    public function getFile()
    {
    	return $this->file;
    }
    
    /**
     * Set file
     *
     * @param UploadedFile $live
     *
     * @return Video
     */
    public function setFile(UploadedFile $file)
    {
    	$this->file = $file;
    	// On vérifie si on avait déjà un fichier pour cette entité
    	if (null !== $this->id) {
    		// On sauvegarde le nom du fichier pour le supprimer plus tard
    		$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;	    	
    	}
    	
    	return $this;    	
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
    	//s'il n'y a pas de fichier, on ne fait pas d'upload
    	if (null === $this->file) {
    		return;
    	}
    
    	// Le nom du fichier est son id, on doit juste stocker également son extension
    	$this->extension = $this->file->guessExtension();
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
    	//s'il n'y a pas de fichier, on ne fait pas d'upload
    	if (null === $this->file) {
    		return;
    	}
    	//si on avait un ancien fichier, on le supprime
    	if (null !== $this->tempFilename) {
    		if (file_exists($this->tempFilename)) {
    			unlink($this->tempFilename);
    		}
    	}
    	//on déplace le fichier dans notre dossier
    	$this->file->move(
    			$this->getUploadRootDir(), 
    			$this->id.'.'.$this->extension
    	);
    }
    
    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
    	// On sauvegarde temporairement le nom du fichier, car il dépend de l'id
    	$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->extension;
    }
    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
    	// En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
    	if (file_exists($this->tempFilename)) {
    		// On supprime le fichier
    		unlink($this->tempFilename);
    	}
    }
    
    public function getUploadDir()
    {
    	// On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
    	return 'uploads/video';
    }
    
    public function getUploadRootDir()
    {
    	// On retourne le chemin relatif vers l'image pour notre code PHP
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Video
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
