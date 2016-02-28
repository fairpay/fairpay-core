<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeSlug;
use Fairpay\Bundle\SchoolBundle\Form\SchoolCreation;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeEmail;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeName;
use Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository;
use Fairpay\Util\Manager\EntityManager;

class SchoolManager extends EntityManager
{
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

    public function updateName(SchoolChangeName $schoolChangeName, School $school)
    {
        $school->setName($schoolChangeName->name);
        $this->em->persist($school);
        $this->em->flush();
    }

    public function updateSlug(SchoolChangeSlug $schoolChangeSlug, School $school)
    {
        $oldSlug = $school->getSlug();
        $school->setSlug($schoolChangeSlug->slug);

        if ($oldSlug) {
            $school->addOldSlug($oldSlug);
        }

        $this->em->persist($school);

        // Remove new slug from others school's old_slugs
        foreach ($this->repo->findWithOldSlug($schoolChangeSlug->slug) as $s) {
            /** @var School $s */
            $slugs = $s->getOldSlugs();
            unset($slugs[array_search($schoolChangeSlug->slug, $slugs)]);
            $s->setOldSlugs($slugs);
            $this->em->persist($s);
        }

        $this->em->flush();
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