<?php


namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Bundle\UserBundle\Validator\Constraints\Password;

class SchoolChoosePassword
{
    /**
     * @Password()
     */
    public $plainPassword;
}