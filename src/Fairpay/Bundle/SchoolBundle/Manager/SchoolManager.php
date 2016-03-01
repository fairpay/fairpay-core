<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;

use Doctrine\ORM\EntityManager as DoctrineEM;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeEmail;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeName;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeSlug;
use Fairpay\Bundle\SchoolBundle\Form\SchoolCreation;
use Fairpay\Bundle\SchoolBundle\Form\SchoolEmailPolicy;
use Fairpay\Util\Email\Services\EmailHelper;
use Fairpay\Util\Manager\EntityManager;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;

class SchoolManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpaySchoolBundle:School';

    /** @var EmailHelper */
    private $emailHelper;

    /**
     * SchoolManager constructor.
     * @param DoctrineEM               $em
     * @param TraceableEventDispatcher $dispatcher
     * @param EmailHelper              $emailHelper
     */
    public function __construct(DoctrineEM $em, TraceableEventDispatcher $dispatcher, EmailHelper $emailHelper)
    {
        parent::__construct($em, $dispatcher);
        $this->emailHelper = $emailHelper;
    }

    /**
     * Create a School with a random registrationToken, save it, and dispatch onSchoolCreated event.
     *
     * @param SchoolCreation $schoolCreation
     */
    public function create(SchoolCreation $schoolCreation)
    {
        $school = new School($schoolCreation->name, $schoolCreation->email);
        $school->setRegistrationToken($this->generateRegistrationToken());
        $this->guessEmailPolicy($school);

        $this->em->persist($school);
        $this->em->flush();

        $this->dispatcher->dispatch(SchoolEvent::onSchoolCreated, new SchoolEvent($school));
    }

    /**
     * Update School's email, generate a new random registrationToken, and dispatch onSchoolChangedEmail event.
     *
     * @param SchoolChangeEmail $schoolChangeEmail
     * @param School            $school
     */
    public function updateEmail(SchoolChangeEmail $schoolChangeEmail, School $school)
    {
        $school->setEmail($schoolChangeEmail->email);
        $school->setRegistrationToken($this->generateRegistrationToken());
        $this->guessEmailPolicy($school);

        $this->em->persist($school);
        $this->em->flush();

        $this->dispatcher->dispatch(SchoolEvent::onSchoolChangedEmail, new SchoolEvent($school));
    }

    /**
     * Update School's name.
     *
     * @param SchoolChangeName $schoolChangeName
     * @param School           $school
     */
    public function updateName(SchoolChangeName $schoolChangeName, School $school)
    {
        $school->setName($schoolChangeName->name);
        $this->em->persist($school);
        $this->em->flush();
    }

    /**
     * Update School's slug.
     *
     * @param SchoolChangeSlug $schoolChangeSlug
     * @param School           $school
     */
    public function updateSlug(SchoolChangeSlug $schoolChangeSlug, School $school)
    {
        $oldSlug = $school->getSlug();
        $school->setSlug($schoolChangeSlug->slug);

        // Save old slug for legacy
        if ($oldSlug) {
            $school->addOldSlug($oldSlug);
        }

        $this->em->persist($school);

        // Remove new slug from others school's old_slugs to prevent conflicts
        foreach ($this->repo->findWithOldSlug($schoolChangeSlug->slug) as $s) {
            /** @var School $s */
            $slugs = $s->getOldSlugs();
            unset($slugs[array_search($schoolChangeSlug->slug, $slugs)]);
            $s->setOldSlugs($slugs);
            $this->em->persist($s);
        }

        $this->em->flush();
    }

    /**
     * Update School's allowedEmailDomains and allowUnregisteredEmails.
     *
     * @param SchoolEmailPolicy $schoolEmailPolicy
     * @param School            $school
     */
    public function updateEmailPolicy(SchoolEmailPolicy $schoolEmailPolicy, School $school)
    {
        $school->setAllowUnregisteredEmails($schoolEmailPolicy->allowUnregisteredEmails);
        $school->setAllowedEmailDomains($schoolEmailPolicy->allowedEmailDomains);
        $this->em->persist($school);
        $this->em->flush();
    }

    /**
     * Update School's allowedEmailDomains and allowUnregisteredEmails based on it's email domain.
     *
     * @param School $school
     * @param bool   $persist
     */
    public function guessEmailPolicy(School $school, $persist = false)
    {
        if ($this->emailHelper->isStandard($school)) {
            $school->setAllowUnregisteredEmails(false);
            $school->setAllowedEmailDomains(null);
        } else {
            $school->setAllowUnregisteredEmails(true);
            $school->setAllowedEmailDomains([$this->emailHelper->getDomain($school)]);
        }

        if ($persist) {
            $this->em->persist($school);
            $this->em->flush();
        }
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