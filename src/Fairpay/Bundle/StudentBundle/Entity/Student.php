<?php

namespace Fairpay\Bundle\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\SchoolBundle\Model\SchoolContext;
use Fairpay\Bundle\UserBundle\Entity\User;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Student
 *
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\StudentBundle\Repository\StudentRepository")
 */
class Student extends SchoolContext
{
    const MALE = 'male';
    const FEMALE = 'female';

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
     * @ORM\Column(name="first_name", type="string", length=100, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="school_year", type="string", length=20, nullable=true)
     */
    private $schoolYear;

    /**
     * @var bool
     * @ORM\Column(name="is_sub", type="boolean")
     */
    private $isSub;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    private $gender;

    /**
     * @var \DateTime
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     * @ORM\Column(name="barcode", type="string", length=255, nullable=true)
     */
    private $barcode;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @var bool
     * @Exclude()
     * @ORM\Column(name="self_registered", type="boolean")
     */
    private $selfRegistered;

    /**
     * @var array
     * @Exclude()
     * @ORM\Column(name="untouchable_fields", type="simple_array", nullable=true)
     */
    private $untouchableFields;

    /**
     * @var SubHistory
     * @ORM\OneToMany(targetEntity="Fairpay\Bundle\StudentBundle\Entity\SubHistory", mappedBy="student", fetch="LAZY")
     */
    protected $subHistory;

    /**
     * @var User
     * @Exclude()
     * @ORM\OneToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="LAZY", inversedBy="student")
     */
    private $user;

    public function __construct($firstName = null, $lastName = null, $email = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->untouchableFields = array();
        $this->isSub = false;
    }

    /**
     * @VirtualProperty()
     * @SerializedName("full_name")
     * @return string
     */
    public function __toString()
    {
        return $this->firstName . ' ' . $this->lastName;
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Student
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Student
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Student
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set schoolYear
     *
     * @param string $schoolYear
     *
     * @return Student
     */
    public function setSchoolYear($schoolYear)
    {
        $this->schoolYear = $schoolYear;

        return $this;
    }

    /**
     * Get schoolYear
     *
     * @return string
     */
    public function getSchoolYear()
    {
        return $this->schoolYear;
    }

    /**
     * Set isSub
     *
     * @param boolean $isSub
     *
     * @return Student
     */
    public function setIsSub($isSub)
    {
        $this->isSub = $isSub;

        return $this;
    }

    /**
     * Get isSub
     *
     * @return bool
     */
    public function getIsSub()
    {
        return $this->isSub;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Student
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Student
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set barcode
     *
     * @param string $barcode
     *
     * @return Student
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * Get barcode
     *
     * @return string
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Student
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set picture
     *
     * @param string $picture
     *
     * @return Student
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set selfRegistered
     *
     * @param boolean $selfRegistered
     *
     * @return Student
     */
    public function setSelfRegistered($selfRegistered)
    {
        $this->selfRegistered = $selfRegistered;

        return $this;
    }

    /**
     * Get selfRegistered
     *
     * @return bool
     */
    public function getSelfRegistered()
    {
        return $this->selfRegistered;
    }

    /**
     * Set untouchableFields
     *
     * @param array $untouchableFields
     *
     * @return Student
     */
    public function setUntouchableFields($untouchableFields)
    {
        $this->untouchableFields = $untouchableFields;

        return $this;
    }

    /**
     * Get untouchableFields
     *
     * @return array
     */
    public function getUntouchableFields()
    {
        return $this->untouchableFields;
    }

    /**
     * Add subHistory
     *
     * @param SubHistory $subHistory
     *
     * @return Student
     */
    public function addSubHistory(SubHistory $subHistory)
    {
        $this->subHistory[] = $subHistory;

        return $this;
    }

    /**
     * Remove subHistory
     *
     * @param SubHistory $subHistory
     */
    public function removeSubHistory(SubHistory $subHistory)
    {
        $this->subHistory->removeElement($subHistory);
    }

    /**
     * Get subHistory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubHistory()
    {
        return $this->subHistory;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @VirtualProperty()
     * @return bool
     */
    public function hasAccount()
    {
        return null !== $this->user;
    }
}
