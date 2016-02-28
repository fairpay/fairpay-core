<?php


namespace Fairpay\Bundle\SchoolBundle\Form\Entity;

use Fairpay\Bundle\SchoolBundle\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="name", entity="FairpaySchoolBundle:School")
 * @UniqueEntity(fields="email", entity="FairpaySchoolBundle:School")
 */
class SchoolRegister
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;
}