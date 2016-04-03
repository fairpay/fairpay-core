<?php


namespace Fairpay\Bundle\UserBundle\Event;


use Fairpay\Bundle\UserBundle\Entity\User;

class UserCreatedEvent extends UserEvent
{
    const REGISTERED_WITH_SCHOOL = 1;
    const SELF_REGISTERED = 2;
    const REGISTERED_BY_ADMIN = 3;

    private $trigger;

    /**
     * UserCreatedEvent constructor.
     * @param User $user
     * @param      $trigger
     */
    public function __construct(User $user, $trigger)
    {
        parent::__construct($user);
        $this->trigger = $trigger;
    }

    /**
     * @param integer|array $trigger
     * @return bool
     */
    public function triggeredBy($trigger)
    {
        return in_array($this->trigger, (array) $trigger);
    }
}