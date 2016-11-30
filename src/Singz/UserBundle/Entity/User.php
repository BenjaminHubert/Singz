<?php

namespace Singz\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="singz_user")
 * @ORM\Entity(repositoryClass="Singz\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Publication
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Publication", mappedBy="user")
     */
    private $publications;
    
    /**
     * @var Love
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Love", mappedBy="user")
     */
    private $loves;
    
    /**
     * @var Comment
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Comment", mappedBy="author")
     */
    private $comments;
    
    /**
     * @var Notification
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Notification", mappedBy="user")
     */
    private $notifications;
    
    /**
     * @var User
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Follow", mappedBy="leader")
     */
    private $leaders;
    
    /**
     * @var User
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Follow", mappedBy="follower")
     */
    private $followers;
    
    /**
     * @var Image
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\Image")
     */
    private $image;
    
    /**
     * @var text
     * @ORM\Column(name="biography", type="text", nullable=true)
     */
    private $biography;
    
    /**
     * @var boolean
     * @ORM\Column(name="is_private", type="boolean", nullable=false)
     */
    private $isPrivate = 0;
    
    public function __construct(){
    	//comportement par défaut de FOS User
    	parent::__construct();
    	//role utilisateur par défaut
    	if(empty($this->roles)){
    		$this->roles[] = 'ROLE_SINGZER';
    	}
    }
    

    /**
     * Add publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return User
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
     * Add love
     *
     * @param \Singz\SocialBundle\Entity\Love $love
     *
     * @return User
     */
    public function addLove(\Singz\SocialBundle\Entity\Love $love)
    {
        $this->loves[] = $love;

        return $this;
    }

    /**
     * Remove love
     *
     * @param \Singz\SocialBundle\Entity\Love $love
     */
    public function removeLove(\Singz\SocialBundle\Entity\Love $love)
    {
        $this->loves->removeElement($love);
    }

    /**
     * Get loves
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLoves()
    {
        return $this->loves;
    }

    /**
     * Add comment
     *
     * @param \Singz\SocialBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\Singz\SocialBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \Singz\SocialBundle\Entity\Comment $comment
     */
    public function removeComment(\Singz\SocialBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add notification
     *
     * @param \Singz\SocialBundle\Entity\Notification $notification
     *
     * @return User
     */
    public function addNotification(\Singz\SocialBundle\Entity\Notification $notification)
    {
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification
     *
     * @param \Singz\SocialBundle\Entity\Notification $notification
     */
    public function removeNotification(\Singz\SocialBundle\Entity\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Add leader
     *
     * @param \Singz\SocialBundle\Entity\Follow $leader
     *
     * @return User
     */
    public function addLeader(\Singz\SocialBundle\Entity\Follow $leader)
    {
        $this->leaders[] = $leader;

        return $this;
    }

    /**
     * Remove leader
     *
     * @param \Singz\SocialBundle\Entity\Follow $leader
     */
    public function removeLeader(\Singz\SocialBundle\Entity\Follow $leader)
    {
        $this->leaders->removeElement($leader);
    }

    /**
     * Get leaders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeaders()
    {
        return $this->leaders;
    }

    /**
     * Add follower
     *
     * @param \Singz\SocialBundle\Entity\Follow $follower
     *
     * @return User
     */
    public function addFollower(\Singz\SocialBundle\Entity\Follow $follower)
    {
        $this->followers[] = $follower;

        return $this;
    }

    /**
     * Remove follower
     *
     * @param \Singz\SocialBundle\Entity\Follow $follower
     */
    public function removeFollower(\Singz\SocialBundle\Entity\Follow $follower)
    {
        $this->followers->removeElement($follower);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Set image
     *
     * @param \Singz\UserBundle\Entity\Image $image
     *
     * @return User
     */
    public function setImage(\Singz\UserBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Singz\UserBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set biography
     *
     * @param string $biography
     *
     * @return User
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     *
     * @return User
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }
}
