<?php


namespace Fairpay\Bundle\UserBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class UserEmail
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;
}