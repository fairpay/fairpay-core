<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Event\SchoolCreatedEvent;
use Fairpay\Bundle\SchoolBundle\Event\SchoolEvents;
use Fairpay\Bundle\SchoolBundle\Form\Entity\SchoolRegister;
use Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository;
use Fairpay\Util\Manager\EntityManager;

class SchoolManager extends EntityManager
{
    /**
     * @var SchoolRepository $repo
     */

    const ENTITY_SHORTCUT_NAME = 'FairpaySchoolBundle:School';

    public function register(SchoolRegister $schoolRegister)
    {
        $school = new School($schoolRegister->name, $schoolRegister->email);
        $school->setRegistrationToken(base64_encode(random_bytes(16)));

        $this->em->persist($school);
        $this->em->flush();

        $this->dispatcher->dispatch(SchoolEvents::onSchoolCreated, new SchoolCreatedEvent($school));
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}