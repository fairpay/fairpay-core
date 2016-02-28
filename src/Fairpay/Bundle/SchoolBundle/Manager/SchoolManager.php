<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;
use Fairpay\Bundle\SchoolBundle\Form\Entity\SchoolCreation;
use Fairpay\Bundle\SchoolBundle\Form\Registration\SchoolChangeEmail;
use Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository;
use Fairpay\Util\Manager\EntityManager;

class SchoolManager extends EntityManager
{
    /**
     * @var SchoolRepository $repo
     */

    const ENTITY_SHORTCUT_NAME = 'FairpaySchoolBundle:School';

    /**
     * Create a School with a random registrationToken, save it, and dispatch onSchoolCreated event
     * @param SchoolCreation $schoolCreation
     */
    public function create(SchoolCreation $schoolCreation)
    {
        $school = new School($schoolCreation->name, $schoolCreation->email);
        $school->setRegistrationToken($this->generateRegistrationToken());

        $this->em->persist($school);
        $this->em->flush();

        $this->dispatcher->dispatch(SchoolEvent::onSchoolCreated, new SchoolEvent($school));
    }

    public function updateEmail(SchoolChangeEmail $schoolChangeEmail, School $school)
    {
        $school->setEmail($schoolChangeEmail->email);
        $school->setRegistrationToken($this->generateRegistrationToken());

        $this->em->persist($school);
        $this->em->flush();

        $this->dispatcher->dispatch(SchoolEvent::onSchoolChangedEmail, new SchoolEvent($school));
    }

    private function generateRegistrationToken()
    {
        return rtrim(strtr(base64_encode(base64_encode(random_bytes(16))), '+/', '-_'), '=');
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}