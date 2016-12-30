<?php

namespace Singz\SocialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="Singz\SocialBundle\Repository\NotificationRepository")
 */
class Notification
{
	const NEW_LOVE = '<b>%s</b> love votre publication.';
	const NEW_COMMENT = '<b>%s</b> a commenté votre publication.';
	const REPLY_COMMENT = '<b>%s</b> a répondu à votre commentaire.';
	
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
     * @ORM\Column(name="is_read", type="boolean")
     */
    private $isRead = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;
    
    /**
     * @var User
     * 
     * @ORM\ManyToOne(targetEntity="Singz\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;
    
    /**
     * @var Publication
     *
     * @ORM\ManyToOne(targetEntity="Singz\SocialBundle\Entity\Publication", inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;
    
    /**
     * @var text
     * 
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

	
    public function __construct(){
    	$this->date = new \DateTime();
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
     * Set isRead
     *
     * @param boolean $isRead
     *
     * @return Notification
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;

        return $this;
    }

    /**
     * Get isRead
     *
     * @return bool
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Notification
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set publication
     *
     * @param \Singz\SocialBundle\Entity\Publication $publication
     *
     * @return Notification
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

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Notification
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set userFrom
     *
     * @param \Singz\UserBundle\Entity\User $userFrom
     *
     * @return Notification
     */
    public function setUserFrom(\Singz\UserBundle\Entity\User $userFrom)
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    /**
     * Get userFrom
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getUserFrom()
    {
        return $this->userFrom;
    }

    /**
     * Set userTo
     *
     * @param \Singz\UserBundle\Entity\User $userTo
     *
     * @return Notification
     */
    public function setUserTo(\Singz\UserBundle\Entity\User $userTo)
    {
        $this->userTo = $userTo;

        return $this;
    }

    /**
     * Get userTo
     *
     * @return \Singz\UserBundle\Entity\User
     */
    public function getUserTo()
    {
        return $this->userTo;
    }
}
