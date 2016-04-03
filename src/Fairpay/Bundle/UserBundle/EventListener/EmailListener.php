<?php


namespace Fairpay\Bundle\UserBundle\EventListener;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Event\UserEvent;
use Fairpay\Bundle\UserBundle\Manager\TokenManager;

class EmailListener extends \Fairpay\Util\EventListener\EmailListener
{
    /** @var  TokenManager */
    private $tokenManager;

    /**
     * EmailListener constructor.
     * @param TokenManager $tokenManager
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            UserEvent::onUserCreated => [['onUserCreated']],
        );
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $user = $event->getUser();

        if ($event->triggeredBy([
            UserCreatedEvent::REGISTERED_BY_ADMIN,
            UserCreatedEvent::SELF_REGISTERED,
        ])) {
            $token = $this->tokenManager->create($user, Token::REGISTER);

            $template = $event->triggeredBy(UserCreatedEvent::REGISTERED_BY_ADMIN) ?
                'FairpayUserBundle:email:user_registered_by_admin.html.twig' :
                'FairpayUserBundle:email:user_self_registered.html.twig';

            $this->send('Hello Email', $user->getEmail(), $this->render(
                $template,
                array(
                    'user' => $user,
                    'token' =>$token,
                )
            ));
        }
    }
}