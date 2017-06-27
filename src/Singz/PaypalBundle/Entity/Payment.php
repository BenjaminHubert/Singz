<?php

namespace Singz\PaypalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Payment
 *
 * @ORM\Table(name="paypal_payment")
 * @ORM\Entity(repositoryClass="Singz\PaypalBundle\Repository\PaymentRepository")
 */
class Payment
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
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=255)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="intent", type="string", length=255)
     */
    private $intent;

    /**
     * @var string
     *
     * @ORM\Column(name="paypal_id", type="string", length=255, unique=true)
     */
    private $paypalId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
    
    /**
     * 
     * @var Transaction
     * 
     * @ORM\OneToMany(targetEntity="Singz\PaypalBundle\Entity\Transaction", mappedBy="payment")
     */
    private $transactions;


    public function __construct()
    {
    	$this->createdAt = new \DateTime();
    	$this->transactions = new ArrayCollection();
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
     * Set state
     *
     * @param string $state
     *
     * @return Payment
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set intent
     *
     * @param string $intent
     *
     * @return Payment
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * Get intent
     *
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * Set paypalId
     *
     * @param string $paypalId
     *
     * @return Payment
     */
    public function setPaypalId($paypalId)
    {
        $this->paypalId = $paypalId;

        return $this;
    }

    /**
     * Get paypalId
     *
     * @return string
     */
    public function getPaypalId()
    {
        return $this->paypalId;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Payment
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
     * Add transaction
     *
     * @param \Singz\PaypalBundle\Entity\Transaction $transaction
     *
     * @return Payment
     */
    public function addTransaction(\Singz\PaypalBundle\Entity\Transaction $transaction)
    {
        $this->transactions[] = $transaction;

        return $this;
    }

    /**
     * Remove transaction
     *
     * @param \Singz\PaypalBundle\Entity\Transaction $transaction
     */
    public function removeTransaction(\Singz\PaypalBundle\Entity\Transaction $transaction)
    {
        $this->transactions->removeElement($transaction);
    }

    /**
     * Get transactions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
