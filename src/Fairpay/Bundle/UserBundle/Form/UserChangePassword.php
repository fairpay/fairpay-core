<?php


namespace Fairpay\Bundle\UserBundle\Form;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class UserChangePassword extends AbstractUserSetPassword
{
    /**
     * @UserPassword()
     */
    public $currentPassword;
}