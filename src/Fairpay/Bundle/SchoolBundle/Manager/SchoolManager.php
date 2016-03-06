<?php

namespace Fairpay\Bundle\SchoolBundle\Manager;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Event\SchoolEvent;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeEmail;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeName;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeSlug;
use Fairpay\Bundle\SchoolBundle\Form\SchoolCreation;
use Fairpay\Bundle\SchoolBundle\Form\SchoolEmailPolicy;
use Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository;
use Fairpay\Util\Email\Services\EmailHelper;
use Fairpay\Util\Manager\EntityManager;
use Fairpay\Util\Util\TokenGeneratorInterface;

/**
 * @property SchoolRepository $repo
 */
class SchoolManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpaySchoolBundle:School';

    /** @var EmailHelper */
    private $emailHelper;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /** @var School */
    private $currentSchool;

    /**
     * SchoolManager constructor.
     * @param EmailHelper             $emailHelper
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(EmailHelper $emailHelper, TokenGeneratorInterface $tokenGenerator)
    {
        $this->emailHelper = $emailHelper;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Get the School matching the subdomain.
     * @return School
     */
    public function getCurrentSchool()
    {
        return $this->currentSchool;
    }

    /**
     * Set the School matching the subdomain.
     * @param string|School $slug
     * @return School|null
     * @throws CurrentSchoolAlreadySetException
     */
    public function setCurrentSchool($slug)
    {
        if ($this->currentSchool) {
            throw new CurrentSchoolAlreadySetException('Current school has already been set.');
        }

        if ($slug instanceof School) {
            $this->currentSchool = $slug;
        } else {
            $this->currentSchool = $this->repo->findOneBy(array('slug' => $slug));
        }

        return $this->currentSchool;
    }

    /**
     * Check if a slug is valid.
     * @param string $slug
     * @return bool
     */
    public function isValidSlug($slug)
    {
        return preg_match('/^[a-z](-?[a-z0-9]+)+$/', $slug) && !in_array($slug, ['api', 'www']);
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
     * Clean up School entity once registration is complete.
     * @param School $school
     */
    public function finishRegistration(School $school)
    {
        $school->setRegistrationToken(null);
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
        return $this->tokenGenerator->generateToken();
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}