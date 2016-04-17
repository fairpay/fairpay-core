<?php

namespace Fairpay\Bundle\TransactionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\SchoolBundle\Model\SchoolContext;
use Fairpay\Bundle\UserBundle\Entity\User;

/**
 * Transaction
 *
 * @ORM\Table(name="transaction")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\TransactionBundle\Repository\TransactionRepository")
 */
class Transaction extends SchoolContext
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
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER")
     */
    private $issuer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER")
     */
    private $receiver;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=7, scale=2)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issued_at", type="datetime")
     */
    private $issuedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER")
     */
    private $issuedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=144, nullable=true)
     */
    private $message;

    /**
     * @var bool
     *
     * @ORM\Column(name="canceled", type="boolean")
     */
    private $canceled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="canceled_at", type="datetime", nullable=true)
     */
    private $canceledAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER")
     */
    private $canceledBy;

    /**
     * @var string
     *
     * @ORM\Column(name="cancel_message", type="string", length=144, nullable=true)
     */
    private $cancelMessage;

    /**
     * Transaction constructor.
     * @param User   $issuer
     * @param User   $receiver
     * @param string $amount
     * @param string $message
     */
    public function __construct(User $issuer = null, User $receiver = null, $amount = null, $message = null)
    {
        $this->issuer   = $issuer;
        $this->receiver = $receiver;
        $this->amount   = $amount;
        $this->message  = $message;
        $this->issuedAt = new \DateTime();
        $this->canceled = false;
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
     * @param string $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set issuedAt
     *
     * @param \DateTime $issuedAt
     *
     * @return Transaction
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt
     *
     * @return \DateTime
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Transaction
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
     * Set canceled
     *
     * @param boolean $canceled
     *
     * @return Transaction
     */
    public function setCanceled($canceled)
    {
        $this->canceled = $canceled;

        return $this;
    }

    /**
     * Get canceled
     *
     * @return bool
     */
    public function getCanceled()
    {
        return $this->canceled;
    }

    /**
     * Set canceledAt
     *
     * @param \DateTime $canceledAt
     *
     * @return Transaction
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    /**
     * Get canceledAt
     *
     * @return \DateTime
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * Set cancelMessage
     *
     * @param string $cancelMessage
     *
     * @return Transaction
     */
    public function setCancelMessage($cancelMessage)
    {
        $this->cancelMessage = $cancelMessage;

        return $this;
    }

    /**
     * Get cancelMessage
     *
     * @return string
     */
    public function getCancelMessage()
    {
        return $this->cancelMessage;
    }

    /**
     * @return User
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param User $issuer
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
    }

    /**
     * @return User
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param User $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return User
     */
    public function getIssuedBy()
    {
        return $this->issuedBy;
    }

    /**
     * @param User $issuedBy
     */
    public function setIssuedBy($issuedBy)
    {
        $this->issuedBy = $issuedBy;
    }

    /**
     * @return User
     */
    public function getCanceledBy()
    {
        return $this->canceledBy;
    }

    /**
     * @param User $canceledBy
     */
    public function setCanceledBy($canceledBy)
    {
        $this->canceledBy = $canceledBy;
    }
}

