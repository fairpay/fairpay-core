<?php


namespace Fairpay\Bundle\SchoolBundle\EventListener;

use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;

class EmailListener extends \Fairpay\Util\EventListener\EmailListener
{
    public static function getSubscribedEvents()
    {
        return array(
            SchoolEvent::onSchoolCreated => [['onSchoolCreated']],
            SchoolEvent::onSchoolChangedEmail => [['onSchoolChangedEmail']],
        );
    }

    /**
     * Send an email with the activation link when a school is created.
     *
     * @param SchoolEvent $event
     */
    public function onSchoolCreated(SchoolEvent $event)
    {
        $school = $event->getSchool();

        $this->send('Hello Email', $school->getEmail(), $this->render(
            'FairpaySchoolBundle:email:school_created.html.twig',
            array('school' => $school)
        ));
    }

    /**
     * Send an email with the new activation link when a school email changes.
     *
     * @param SchoolEvent $event
     */
    public function onSchoolChangedEmail(SchoolEvent $event)
    {
        $school = $event->getSchool();

        $this->send('Hello Email', $school->getEmail(), $this->render(
            'FairpaySchoolBundle:email:school_changed_email.html.twig',
            array('school' => $school)
        ));
    }
}