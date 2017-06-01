<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\ReportRepository")
 */
class Report
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
     * @var User $reporter
     *
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reporter;
    
    /**
     * @var Comment $comment
     * 
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Comment", inversedBy="reports")
     * @ORM\JoinColumn(nullable=true)
     */
    private $comment = NULL;
    
    /**
     * 
     * @var DateTime $createdAt
     * 
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    /**
     * @var Publication $publication
     * 
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Publication", inversedBy="reports")
     * @ORM\JoinColumn(nullable=true)
     */
    private $publication = NULL;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function __construct(){
    	$this->createdAt = new \DateTime();
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Report
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
     * Set reporter
     *
     * @param \Singz\UserBundle\Entity\User $reporter
     *
     * @return Report
     */
    public function setReporter(\Singz\UserBundle\Entity\User $reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set comment
     *
     * @param \Singz\SocialBundle\Entity\Comment $comment
     *
     * @return Report
     */
    public function setComment(\Singz\SocialBundle\Entity\Comment $comment = NULL)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \Singz\SocialBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return Report
     */
    public function setPublication(\Singz\SocialBundle\Entity\Publication $publication = null)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication
     *
     * @return \Singz\SocialBundle\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }
}
