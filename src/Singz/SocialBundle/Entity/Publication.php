<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\PublicationRepository")
 */
class Publication
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @var Love
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Love", mappedBy="publication")
     */
    private $loves;
    
    /**
     * @var Comment
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Comment", mappedBy="publication")
     */
    private $comments;
    
    /**
     * @var Notification
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Notification", mappedBy="publication")
     */
    private $notifications;
    
    /**
     * @var Video
     * 
     * @ORM\ManyToOne(targetEntity="Singz\VideoBundle\Entity\Video", inversedBy="publications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $video;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loves = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Publication
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Publication
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     *
     * @param \Singz\UserBundle\Entity\User $user
     *
     * @return Publication
     */
    public function setUser(\Singz\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add love
     *
     * @param \Singz\SocialBundle\Entity\Love $love
     *
     * @return Publication
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
     * @return Publication
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
     * @return Publication
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
     * Set video
     *
     * @param \Singz\VideoBundle\Entity\Video $video
     *
     * @return Publication
     */
    public function setVideo(\Singz\VideoBundle\Entity\Video $video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return \Singz\VideoBundle\Entity\Video
     */
    public function getVideo()
    {
        return $this->video;
    }
}
