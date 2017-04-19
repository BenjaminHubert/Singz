<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Comment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $children;
    
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
    private $state;
    
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
     * Set children
     *
     * @param \Singz\SocialBundle\Entity\Comment $children
     *
     * @return Comment
     */
    public function setChildren(\Singz\SocialBundle\Entity\Comment $children = null)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return \Singz\SocialBundle\Entity\Comment
     */
    public function getChildren()
    {
        return $this->children;
    }
}
