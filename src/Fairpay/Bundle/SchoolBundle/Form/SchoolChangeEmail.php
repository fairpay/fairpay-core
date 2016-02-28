<?php


namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Util\Email\Validator\Constraints\NotDisposableEmail;
use Symfony\Component\Validator\Constraints as Assert;
use Fairpay\Util\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields="email", entity="FairpaySchoolBundle:School")
 */
class SchoolChangeEmail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @NotDisposableEmail()
     */
    public $email;
}