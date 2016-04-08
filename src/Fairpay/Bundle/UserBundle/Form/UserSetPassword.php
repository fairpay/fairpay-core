<?php


namespace Fairpay\Bundle\UserBundle\Form;


use Fairpay\Bundle\UserBundle\Validator\Constraints\Password;

class UserSetPassword
{
    /**
     * @Password()
     */
    public $plainPassword;
}