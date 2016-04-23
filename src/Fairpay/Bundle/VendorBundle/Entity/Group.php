<?php

namespace Fairpay\Bundle\VendorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\UserBundle\Entity\User;

/**
 * RoleGroup
 *
 * @ORM\Table(name="vendor_permission_group")
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
     * @var array
     *
     * @ORM\Column(name="mask", type="integer")
     */
    private $mask;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\UserBundle\Entity\User", fetch="EAGER", inversedBy="groups")
     */
    private $vendor;


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
}

