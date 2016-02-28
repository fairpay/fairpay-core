<?php


namespace Fairpay\Bundle\SchoolBundle\Form\Entity;

use Fairpay\Util\Email\Validator\Constraints\NotDisposableEmail;
use Fairpay\Util\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="name", entity="FairpaySchoolBundle:School")
 * @UniqueEntity(fields="email", entity="FairpaySchoolBundle:School")
 */
class SchoolCreation
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @NotDisposableEmail()
     */
    public $email;
}