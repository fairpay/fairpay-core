<?php


namespace Fairpay\Bundle\UserBundle\Event;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;

class UserRequestResetPassword extends UserEvent
{
    /** @var  Token */
    private $token;

    /**
     * UserEvent constructor.
     * @param User  $user
     * @param Token $token
     */
    public function __construct(User $user, Token $token)
    {
        parent::__construct($user);
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}