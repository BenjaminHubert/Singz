<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Singz\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\PublicationRepository")
 */
class Publication
{
	const STATE_VISIBLE = 0;
	const STATE_DELETED = 1;
	const STATE_SPAM = 2;
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
     * @var \DateTime
     *
     * @ORM\Column(name="last_edit", type="datetime")
     */
    private $lastEdit;
    
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
     * @var Notification
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Notification", mappedBy="publication")
     */
    private $notifications;
    
    /**
     * @var Video
     * 
     * @ORM\ManyToOne(targetEntity="Singz\VideoBundle\Entity\Video", inversedBy="publications", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Valid()
     */
    private $video;
    
    /**
     * @var int
     * 
     * @ORM\Column(name="num_loves", type="integer")
     */
    private $numLoves = 0;
    
    /**
     * @var Thread
     *
     * @ORM\OneToOne(targetEntity="Singz\SocialBundle\Entity\Thread", mappedBy="publication")
     */
    private $thread;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="resingz")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_resingz", type="boolean", nullable=false)
     */
    private $isResingz = false;
    
    /**
     * 
     * @var int $state
     * 
     * @ORM\Column(type="integer", name="state")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state = Publication::STATE_VISIBLE;
    
    /**
     * @var Report $reports
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Report", mappedBy="publication")
     * @ORM\JoinColumn(nullable=true)
     */
    private $reports;
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->date = new \DateTime();
    	$this->lastEdit = new \DateTime();
        $this->loves = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reports = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set numLoves
     *
     * @param integer $numLoves
     *
     * @return Publication
     */
    public function setNumLoves($numLoves)
    {
        $this->numLoves = $numLoves;

        return $this;
    }

    /**
     * Get numLoves
     *
     * @return integer
     */
    public function getNumLoves()
    {
        return $this->numLoves;
    }
    
    public function increaseNumLoves(){
    	$this->numLoves++;
    	return $this;
    }
    
    public function decreaseNumLoves(){
    	$this->numLoves--;
    	return $this;
    }

    /**
     * Set lastEdit
     *
     * @param \DateTime $lastEdit
     *
     * @return Publication
     */
    public function setLastEdit($lastEdit)
    {
        $this->lastEdit = $lastEdit;

        return $this;
    }

    /**
     * Get lastEdit
     *
     * @return \DateTime
     */
    public function getLastEdit()
    {
        return $this->lastEdit;
    }

    /**
     * Set thread
     *
     * @param \Singz\SocialBundle\Entity\Thread $thread
     *
     * @return Publication
     */
    public function setThread(\Singz\SocialBundle\Entity\Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \Singz\SocialBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set owner
     *
     * @param \Singz\UserBundle\Entity\User $user
     *
     * @return Publication
     */
    public function setOwner(\Singz\UserBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set isResingz
     *
     * @param boolean $isResingz
     *
     * @return Publication
     */
    public function setIsResingz($isResingz)
    {
        $this->isResingz = $isResingz;

        return $this;
    }

    /**
     * Get isResingz
     *
     * @return boolean
     */
    public function getIsResingz()
    {
        return $this->isResingz;
    }

    /**
     * Set state
     *
     * @param integer $state
     *
     * @return Publication
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Add report
     *
     * @param \Singz\SocialBundle\Entity\Report $report
     *
     * @return Publication
     */
    public function addReport(\Singz\SocialBundle\Entity\Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \Singz\SocialBundle\Entity\Report $report
     */
    public function removeReport(\Singz\SocialBundle\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReports()
    {
        return $this->reports;
    }
}
