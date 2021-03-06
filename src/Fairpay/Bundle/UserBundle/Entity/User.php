<?php

namespace Fairpay\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\SchoolBundle\Model\SchoolContext;
use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\VendorBundle\Entity\Group;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\UserBundle\Repository\UserRepository")
 */
class User extends SchoolContext implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=50)
     */
    private $username;

    /**
     * @var string
     * @SerializedName("full_name")
     * @ORM\Column(name="display_name", type="string", length=50)
     */
    private $displayName;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     * @Exclude()
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     * @Exclude()
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var string
     * @ORM\Column(name="is_vendor", type="boolean")
     */
    private $isVendor;

    /**
     * @var float
     * @ORM\Column(name="balance", type="decimal", precision=7, scale=2)
     */
    private $balance;

    /**
     * @var array
     * @ORM\Column(name="roles", type="json_array")
     */
    private $permissions;

    /**
     * @var Student
     * @ORM\OneToOne(targetEntity="Fairpay\Bundle\StudentBundle\Entity\Student", fetch="EXTRA_LAZY", mappedBy="user")
     */
    private $student;

    /**
     * @var Collection
     * @Exclude()
     * @ORM\OneToMany(targetEntity="Fairpay\Bundle\VendorBundle\Entity\Group", fetch="EXTRA_LAZY", mappedBy="vendor")
     */
    private $groups;

    public function __construct()
    {
        $this->isVendor = true;
        $this->balance = 0;
        $this->permissions = ['global' => 0];
    }

    public function __toString()
    {
        return $this->displayName;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return User
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param array $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }

    /**
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $this->getId();
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
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return string
     */
    public function getIsVendor()
    {
        return $this->isVendor;
    }

    /**
     * @param string $isVendor
     */
    public function setIsVendor($isVendor)
    {
        $this->isVendor = $isVendor;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Add group
     *
     * @param Group $group
     *
     * @return User
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
