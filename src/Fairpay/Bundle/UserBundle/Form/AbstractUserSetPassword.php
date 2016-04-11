<?php


namespace Fairpay\Bundle\UserBundle\Form;


use Fairpay\Bundle\UserBundle\Validator\Constraints\Password;

abstract class AbstractUserSetPassword
{
    /**
     * @Password()
     */
    public $plainPassword;
}