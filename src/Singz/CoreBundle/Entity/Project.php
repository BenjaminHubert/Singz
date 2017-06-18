<?php

namespace Singz\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Singz\CoreBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
	const STATE_VISIBLE = 0;
	const STATE_DELETED = 1;
	const STATE_DONE = 2;
	
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotNull()
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requester;
    
    /**
     * @var int $state
     * 
     * @ORM\Column(type="integer", name="state", nullable=false)
     */
    private $state = Project::STATE_VISIBLE;
    
    /**
     * @var float $amountReached
     * 
     * @ORM\Column(type="float", name="amount_reached", nullable=false)
     */
    private $amountReached = 0;
    
    /**
     * @var datetime $createdAt
     * 
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     */
    private $createdAt;
    
    /**
     * @var datetime $lastEditAt
     * 
     * @ORM\Column(type="datetime", name="last_edit_at", nullable=false)
     */
    private $lastEditAt;
    
    /**
     * @var Notification
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Notification", mappedBy="project")
     */
    private $notifications;
    
    private $amountReachedPercentage = 0;
    
    /**
     * @var Contribution
     * 
     * @ORM\OneToMany(targetEntity="Singz\CoreBundle\Entity\Contribution", mappedBy="project")
     */
    private $contributions;

    public function __construct()
    {
    	$this->createdAt = new \DateTime();
    	$this->lastEditAt = new \DateTime();
    	$this->notifications = new ArrayCollection();
    	$this->contributions = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
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
     * Set user
     *
     * @param \Singz\UserBundle\Entity\User $user
     *
     * @return Project
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
     * Set state
     *
     * @param integer $state
     *
     * @return Project
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
     * Set requester
     *
     * @param \Singz\UserBundle\Entity\User $requester
     *
     * @return Project
     */
    public function setRequester(\Singz\UserBundle\Entity\User $requester)
    {
        $this->requester = $requester;

        return $this;
    }

    /**
     * Get requester
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * Set amountReached
     *
     * @param float $amountReached
     *
     * @return Project
     */
    public function setAmountReached($amountReached)
    {
        $this->amountReached = $amountReached;

        return $this;
    }

    /**
     * Get amountReached
     *
     * @return float
     */
    public function getAmountReached()
    {
        return $this->amountReached;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Project
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set lastEditAt
     *
     * @param \DateTime $lastEditAt
     *
     * @return Project
     */
    public function setLastEditAt($lastEditAt)
    {
        $this->lastEditAt = $lastEditAt;

        return $this;
    }

    /**
     * Get lastEditAt
     *
     * @return \DateTime
     */
    public function getLastEditAt()
    {
        return $this->lastEditAt;
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
    	$this->lastEditAt = new \DateTime();
    }

    /**
     * Add notification
     *
     * @param \Singz\SocialBundle\Entity\Notification $notification
     *
     * @return Project
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
     * @ORM\PostLoad
     */
    public function setAmountReachedPercentage(LifecycleEventArgs $args)
    {
    	$em = $args->getEntityManager();
    	$setting = $em->getRepository('SingzAdminBundle:Setting')->findOneBy(array(
    		'name' => 'Cagnotte'
    	));
    	if(!$setting){
    		$this->amountReachedPercentage = 0;
    		return;
    	}
    	if($setting->getValue() == 0){
    		$this->amountReachedPercentage = 0;
    		return;
    	}
    	$this->amountReachedPercentage = round(($this->amountReached*100)/$setting->getValue(), 2);
    	if($this->amountReachedPercentage > 100){
    		$this->amountReachedPercentage = 100;
    	}
    }
    
    /**
     * Get amountReachedPercentage
     *
     * return float
     */
    public function getAmountReachedPercentage()
    {
    	return $this->amountReachedPercentage;
    }

    /**
     * Add contribution
     *
     * @param \Singz\CoreBundle\Entity\Contribution $contribution
     *
     * @return Project
     */
    public function addContribution(\Singz\CoreBundle\Entity\Contribution $contribution)
    {
        $this->contributions[] = $contribution;

        return $this;
    }

    /**
     * Remove contribution
     *
     * @param \Singz\CoreBundle\Entity\Contribution $contribution
     */
    public function removeContribution(\Singz\CoreBundle\Entity\Contribution $contribution)
    {
        $this->contributions->removeElement($contribution);
    }

    /**
     * Get contributions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContributions()
    {
        return $this->contributions;
    }
}
