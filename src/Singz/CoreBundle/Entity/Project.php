<?php

namespace Singz\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Singz\CoreBundle\Repository\ProjectRepository")
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
    
    public function getAmountReachedPercentage()
    {
    	return $this->amountReached;
    }
}