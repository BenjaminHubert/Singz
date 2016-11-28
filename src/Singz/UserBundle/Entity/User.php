<?php

namespace Singz\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="singz_user")
 * @ORM\Entity(repositoryClass="Singz\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
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
     * @var Publication
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Publication", mappedBy="user")
     */
    private $publications;
    
    /**
     * @var Love
     * 
     * @ORM\OneToMany(targetEntity="Singz\SocialBundle\Entity\Love", mappedBy="user")
     */
    private $loves;
    

    /**
     * Add publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return User
     */
    public function addPublication(\Singz\SocialBundle\Entity\Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     */
    public function removePublication(\Singz\SocialBundle\Entity\Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * Add love
     *
     * @param \Singz\SocialBundle\Entity\Love $love
     *
     * @return User
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
}
