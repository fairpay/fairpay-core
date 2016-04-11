<?php


namespace Fairpay\Bundle\UserBundle\Event;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;

class UserCreatedEvent extends UserEvent
{
    const REGISTERED_WITH_SCHOOL = 1;
    const SELF_REGISTERED = 2;
    const REGISTERED_BY_ADMIN = 3;

    private $trigger;

    /** @var  Token */
    private $token;

    /**
     * UserCreatedEvent constructor.
     * @param User $user
     * @param      $trigger
     */
    public function __construct(User $user, $trigger, Token $token)
    {
        parent::__construct($user);
        $this->trigger = $trigger;
        $this->token = $token;
    }

    /**
     * @param integer|array $trigger
     * @return bool
     */
    public function triggeredBy($trigger)
    {
        return in_array($this->trigger, (array) $trigger);
    }

    /**
     * @return mixed
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }
}