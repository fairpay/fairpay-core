<?php

namespace Fairpay\Bundle\VendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\UserBundle\Entity\User;

/**
 * RoleGroup
 *
 * @ORM\Table(name="permission_group")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\VendorBundle\Repository\RoleGroupRepository")
 */
class Group
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="mask", type="integer")
     */
    private $mask;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="LAZY", inversedBy="groups")
     */
    private $vendor;

    /**
     * @var integer[]
     *
     * @ORM\Column(name="users_ids", type="simple_array", nullable=true)
     */
    private $users;

    /**
     * Group constructor.
     * @param string  $name
     * @param integer $mask
     * @param User    $vendor
     */
    public function __construct($name = null, $mask = 0, User $vendor = null)
    {
        $this->name   = $name;
        $this->mask   = $mask;
        $this->vendor = $vendor;
        $this->users  = [];
    }

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     *
     * @return RoleGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getMask()
    {
        return $this->mask;
    }

    /**
     * @param array $mask
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }

    /**
     * @return User
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param User $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return \integer[]
     */
    public function getUsers()
    {
        return array_filter($this->users);
    }

    /**
     * @param \integer[] $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user->getId();
        $this->users = array_unique($this->users);
    }

    public function removeUser(User $user)
    {
        if (false !== $key = array_search($user->getId(), $this->users)) {
            unset($this->users[$key]);
        }
    }
}

