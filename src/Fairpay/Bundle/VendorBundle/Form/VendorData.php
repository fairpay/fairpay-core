<?php


namespace Fairpay\Bundle\VendorBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class VendorData
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