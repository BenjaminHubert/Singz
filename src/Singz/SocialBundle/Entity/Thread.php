<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Thread
 * 
 * @ORM\Table(name="thread")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\ThreadRepository")
 */
class Thread
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
     * 
     * @var int $numComments
     * 
     * @ORM\Column(type="integer", name="num_comments")
     */
    private $numComments = 0;
    
    /**
     * 
     * @var DateTime $lastCommentAt
     * 
     * @ORM\Column(type="datetime", name="last_comment_at")
     */
    private $lastCommentAt;
    
    /**
     * @var Comment
     *
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Comment", mappedBy="thread")
     */
    private $comments;
    
    /**
     * @var Publication
     *
     * @ORM\OneToOne(targetEntity="Singz\SocialBundle\Entity\Publication", inversedBy="thread")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lastCommentAt = new \DateTime();
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
     * Set numComments
     *
     * @param integer $numComments
     *
     * @return Thread
     */
    public function setNumComments($numComments)
    {
        $this->numComments = $numComments;

        return $this;
    }

    /**
     * Get numComments
     *
     * @return integer
     */
    public function getNumComments()
    {
        return $this->numComments;
    }

    /**
     * Set lastCommentAt
     *
     * @param \DateTime $lastCommentAt
     *
     * @return Thread
     */
    public function setLastCommentAt($lastCommentAt)
    {
        $this->lastCommentAt = $lastCommentAt;

        return $this;
    }

    /**
     * Get lastCommentAt
     *
     * @return \DateTime
     */
    public function getLastCommentAt()
    {
        return $this->lastCommentAt;
    }

    /**
     * Add comment
     *
     * @param \Singz\SocialBundle\Entity\Comment $comment
     *
     * @return Thread
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
     * @param array $states
     * @return \Singz\SocialBundle\Entity\Comment
     */
    public function getComments(Array $states = null)
    {
    	if($states == null){
    		return $this->comments;
    	}
    	
    	$comments = [];
    	foreach($this->comments as $comment){
    		if(in_array($comment->getState(), $states)){
    			$comments[] = $comment;
    		}
    	}
    	return $comments;
    }

    /**
     * Set publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return Thread
     */
    public function setPublication(\Singz\SocialBundle\Entity\Publication $publication)
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

    public function increaseNumComments(){
    	$this->numComments++;
    	return $this;
    }
    
    public function decreaseNumComments(){
    	$this->numComments--;
    	return $this;
    }
}
