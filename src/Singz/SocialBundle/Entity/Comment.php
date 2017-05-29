<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\CommentRepository")
 */
class Comment
{
	const STATE_VISIBLE = 0;
	const STATE_DELETED = 1;
	const STATE_SPAM = 2;
	const STATE_PENDING = 3;
	
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;


    /**
     * @var Thread
     * 
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Thread", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $thread;
    
    /**
     * @var text $body
     * 
     * @ORM\Column(name="body", type="text")
     */
    private $body;
    
    /**
     * @var Comment $children
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Comment", mappedBy="parent")
     * @ORM\JoinColumn(nullable=true)
     */
    private $children = NULL;
    
    /**
     * @var Comment $parent
     * 
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Comment", inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent = NULL;
    
    /**
     * 
     * @var DateTime $createdAt
     * 
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
    
    /**
     * 
     * @var int $state
     * 
     * @ORM\Column(type="integer", name="state")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state = Comment::STATE_VISIBLE;
    
    /**
     * @var Report $reports
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Report", mappedBy="comment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $reports;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->children = new ArrayCollection();
        $this->reports = new ArrayCollection();
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
     * Set body
     *
     * @param string $body
     *
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
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
     * Set state
     *
     * @param integer $state
     *
     * @return Comment
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
     * Set author
     *
     * @param \Singz\UserBundle\Entity\User $author
     *
     * @return Comment
     */
    public function setAuthor(\Singz\UserBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set thread
     *
     * @param \Singz\SocialBundle\Entity\Thread $thread
     *
     * @return Comment
     */
    public function setThread(\Singz\SocialBundle\Entity\Thread $thread)
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
     * Add child
     *
     * @param \Singz\SocialBundle\Entity\Comment $child
     *
     * @return Comment
     */
    public function addChild(\Singz\SocialBundle\Entity\Comment $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Singz\SocialBundle\Entity\Comment $child
     */
    public function removeChild(\Singz\SocialBundle\Entity\Comment $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \Singz\SocialBundle\Entity\Comment $parent
     *
     * @return Comment
     */
    public function setParent(\Singz\SocialBundle\Entity\Comment $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Singz\SocialBundle\Entity\Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add report
     *
     * @param \Singz\SocialBundle\Entity\Comment $report
     *
     * @return Comment
     */
    public function addReport(\Singz\SocialBundle\Entity\Comment $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \Singz\SocialBundle\Entity\Comment $report
     */
    public function removeReport(\Singz\SocialBundle\Entity\Comment $report)
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
