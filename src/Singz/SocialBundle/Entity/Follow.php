<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Follow
 *
 * @ORM\Table(name="follow")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\FollowRepository")
 */
class Follow
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
     * @var bool
     *
     * @ORM\Column(name="is_pending", type="boolean")
     */
    private $isPending;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="leaders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leader;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User", inversedBy="followers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $follower;


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
     * Set isPending
     *
     * @param boolean $isPending
     *
     * @return Follow
     */
    public function setIsPending($isPending)
    {
        $this->isPending = $isPending;

        return $this;
    }

    /**
     * Get isPending
     *
     * @return bool
     */
    public function getIsPending()
    {
        return $this->isPending;
    }

    /**
     * Set leader
     *
     * @param \Singz\UserBundle\Entity\User $leader
     *
     * @return Follow
     */
    public function setLeader(\Singz\UserBundle\Entity\User $leader)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set follower
     *
     * @param \Singz\UserBundle\Entity\User $follower
     *
     * @return Follow
     */
    public function setFollower(\Singz\UserBundle\Entity\User $follower)
    {
        $this->follower = $follower;

        return $this;
    }

    /**
     * Get follower
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getFollower()
    {
        return $this->follower;
    }
}
