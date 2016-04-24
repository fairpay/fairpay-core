<?php


namespace Fairpay\Bundle\VendorBundle\Form;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\VendorBundle\Entity\Group;

class GroupAssignment
{
    /** @var  Group */
    public $group;

    /** @var  User */
    public $user;
}