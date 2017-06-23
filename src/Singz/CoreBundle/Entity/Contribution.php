<?php

namespace Singz\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contribution
 *
 * @ORM\Table(name="contribution")
 * @ORM\Entity(repositoryClass="Singz\CoreBundle\Repository\ContributionRepository")
 */
class Contribution
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
     * @var float
     * @Assert\Range(
     *      min = 1,
     * )
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contributer;
    
    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Singz\CoreBundle\Entity\Project", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $isPrivate = false;

    /**
     * Constructor
     */
	public function __construct()
	{
		$this->createdAt = new \DateTime();
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
     * Set amount
     *
     * @param float $amount
     *
     * @return Contribution
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Contribution
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
     * Set contributer
     *
     * @param \Singz\UserBundle\Entity\User $contributer
     *
     * @return Contribution
     */
    public function setContributer(\Singz\UserBundle\Entity\User $contributer)
    {
        $this->contributer = $contributer;

        return $this;
    }

    /**
     * Get contributer
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getContributer()
    {
        return $this->contributer;
    }

    /**
     * Set project
     *
     * @param \Singz\CoreBundle\Entity\Project $project
     *
     * @return Contribution
     */
    public function setProject(\Singz\CoreBundle\Entity\Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Singz\CoreBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set isPrivate
     *
     * @param boolean $isPrivate
     *
     * @return Contribution
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
