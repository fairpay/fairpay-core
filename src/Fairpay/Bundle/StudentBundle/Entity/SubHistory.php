<?php

namespace Fairpay\Bundle\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\UserBundle\Entity\User;

/**
 * SubHistory
 *
 * @ORM\Table(name="sub_history")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\StudentBundle\Repository\SubHistoryRepository")
 */
class SubHistory
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean")
     */
    private $state;

    /**
     * @var Student
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\StudentBundle\Entity\Student", inversedBy="subHistory")
     */
    protected $student;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User")
     */
    protected $updatedBy;

    /**
     * SubHistory constructor.
     * @param bool    $state
     * @param Student $student
     * @param User    $updatedBy
     */
    public function __construct($state, Student $student, User $updatedBy)
    {
        $this->state     = $state;
        $this->student   = $student;
        $this->updatedBy = $updatedBy;
        $this->createdAt = new \DateTime();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SubHistory
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
     * @param boolean $state
     *
     * @return SubHistory
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return bool
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param Student $student
     *
     * @return SubHistory
     */
    public function setStudent($student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @param User $updatedBy
     *
     * @return SubHistory
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}

