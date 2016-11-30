<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\CommentRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment
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
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @var Publication
     *
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Publication", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;


    /**
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Thread")
     */
    protected $thread;

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
     * Set user
     *
     * @param \Singz\UserBundle\Entity\User $user
     *
     * @return Comment
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
     * Set publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return Comment
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
}
