<?php


namespace Fairpay\Bundle\UserBundle\Event;


use Fairpay\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
    const onUserCreated = 'fairpay.user.created';

    /** @var  User */
    private $user;

    /**
     * UserEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}