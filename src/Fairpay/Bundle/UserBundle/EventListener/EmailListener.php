<?php


namespace Fairpay\Bundle\UserBundle\EventListener;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Event\UserEvent;
use Fairpay\Bundle\UserBundle\Event\UserRequestResendRegistrationEmail;
use Fairpay\Bundle\UserBundle\Event\UserRequestResetPassword;

class EmailListener extends \Fairpay\Util\EventListener\EmailListener
{
    static $userCreatedTemplates = array(
        UserCreatedEvent::REGISTERED_WITH_SCHOOL => null,
        UserCreatedEvent::REGISTERED_BY_ADMIN => 'FairpayUserBundle:email:user_registered_by_admin.html.twig',
        UserCreatedEvent::SELF_REGISTERED => 'FairpayUserBundle:email:user_self_registered.html.twig',
    );

    public static function getSubscribedEvents()
    {
        return array(
            UserEvent::onUserCreated => [['onUserCreated']],
            UserEvent::onUserRequestResetPassword => [['onUserRequestResetPassword']],
        );
    }

    public function onUserCreated(UserCreatedEvent $event)
    {
        $template = self::$userCreatedTemplates[$event->getTrigger()];

        if ($template) {
            $user = $event->getUser();

            $this->send('Hello Email', $user->getEmail(), $this->render(
                $template,
                array(
                    'user' => $user,
                    'token' => $event->getToken(),
                )
            ));
        }
    }

    public function onUserRequestResetPassword(UserRequestResetPassword $event)
    {
        $user = $event->getUser();

        $this->send('Hello Email', $user->getEmail(), $this->render(
            'FairpayUserBundle:email:user_request_reset_password.html.twig',
            array(
                'user' => $user,
                'token' => $event->getToken(),
                'finish_registration' => $event->getToken()->getType() == Token::REGISTER,
            )
        ));
    }
}