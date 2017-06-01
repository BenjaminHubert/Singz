<?php

namespace Singz\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="Singz\UserBundle\Repository\ImageRepository")
 */
class Image
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
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path = null;
    
    /**
     * @var File
     * @Assert\File(
     *     maxSize = "10M",
     *     mimeTypes = {
	 *			"image/jpg",
	 *			"image/jpeg",
	 *			"image/png",
	 *			"image/tiff"
     *     },
     *     mimeTypesMessage = "Le type d'image ({{ type }}) n'est pas correct. Les types autorisés sont uniquement des fichiers images."
     * )
     */
    private $file;


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
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    public function getRealPath(){
    	if($this->path == null){
    		return 'bundles/singzuser/img/anonymous_icon.jpg';
    	}
    	return $this->getUploadDir().$this->path;
    }
    
    public function getUploadRootDir()
    {
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    public function getUploadDir()
    {
    	return 'uploads/userImage/';
    }
    
    /**
     * Get File
     *
     * @return File
     */
    public function getFile()
    {
    	return $this->file;
    }
    
    /**
     * Set file
     *
     * @param File $live
     *
     * @return Video
     */
    public function setFile(UploadedFile  $file)
    {
    	$this->file = $file;
    	
    	return $this;    	
    }
}
