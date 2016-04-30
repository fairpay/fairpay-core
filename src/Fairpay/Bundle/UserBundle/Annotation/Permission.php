<?php


namespace Fairpay\Bundle\UserBundle\Annotation;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 */
class Permission extends ConfigurationAnnotation
{
    protected $role;
    protected $vendor;

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    public function setValue($role)
    {
        $this->setRole($role);
    }

    public function getAliasName()
    {
        return 'permission';
    }

    public function allowArray()
    {
        return false;
    }
}