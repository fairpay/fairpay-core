<?php


namespace Fairpay\Bundle\SchoolBundle\EventListener;

use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailListener implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * EmailListener constructor.
     * @param \Swift_Mailer     $mailer
     * @param \Twig_Environment $twig
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SchoolEvent::onSchoolCreated => [['onSchoolCreated']],
            SchoolEvent::onSchoolChangedEmail => [['onSchoolChangedEmail']],
        );
    }

    /**
     * Send a message with the activation link when a school is created.
     *
     * @param SchoolEvent $event
     */
    public function onSchoolCreated(SchoolEvent $event)
    {
        $school = $event->getSchool();

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo($school->getEmail())
            ->setBody(
                $this->twig->render(
                    'FairpaySchoolBundle:email:school_creation.html.twig',
                    array('school' => $school)
                )
            )
        ;
        $this->mailer->send($message);
    }

    /**
     * Send a message with the new activation link when a school email changes.
     *
     * @param SchoolEvent $event
     */
    public function onSchoolChangedEmail(SchoolEvent $event)
    {
        $school = $event->getSchool();

        $message = \Swift_Message::newInstance()
            ->setSubject('Hello Email')
            ->setFrom('send@example.com')
            ->setTo($school->getEmail())
            ->setBody(
                $this->twig->render(
                    'FairpaySchoolBundle:email:school_changed_email.html.twig',
                    array('school' => $school)
                )
            )
        ;
        $this->mailer->send($message);
    }
}