<?php

namespace Fairpay\Bundle\SchoolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * School
 *
 * @ORM\Table(name="school")
 * @ORM\Entity(repositoryClass="Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository")
 */
class School
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=100, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="slug", type="string", length=20, unique=true, nullable=true)
     */
    private $slug;

    /**
     * @var array
     * @ORM\Column(name="old_slugs", type="simple_array", nullable=true)
     */
    private $oldSlugs;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="registration_token", type="string", length=255, nullable=true)
     */
    private $registrationToken;

    /**
     * @var bool
     * @ORM\Column(name="allow_unregistered_emails", type="boolean")
     */
    private $allowUnregisteredEmails;

    /**
     * @var array
     * @ORM\Column(name="allowed_email_domains", type="simple_array", nullable=true)
     */
    private $allowedEmailDomains;

    /**
     * @var array
     *
     * @ORM\Column(name="school_years", type="json_array", nullable=true)
     */
    private $schoolYears;

    public function __construct($name = null, $email = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->allowUnregisteredEmails = false;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return School
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return School
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set oldSlugs
     *
     * @param array $oldSlugs
     *
     * @return School
     */
    public function setOldSlugs($oldSlugs)
    {
        $this->oldSlugs = $oldSlugs;

        return $this;
    }

    /**
     * Get oldSlugs
     *
     * @return array
     */
    public function getOldSlugs()
    {
        return $this->oldSlugs;
    }

    public function addOldSlug($slug)
    {
        if (!is_array($this->oldSlugs)) {
            $this->oldSlugs = array();
        }

        $this->oldSlugs[] = $slug;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return School
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set registrationToken
     *
     * @param string $registrationToken
     *
     * @return School
     */
    public function setRegistrationToken($registrationToken)
    {
        $this->registrationToken = $registrationToken;

        return $this;
    }

    /**
     * Get registrationToken
     *
     * @return string
     */
    public function getRegistrationToken()
    {
        return $this->registrationToken;
    }

    /**
     * Set allowUnregisteredEmails
     *
     * @param boolean $allowUnregisteredEmails
     *
     * @return School
     */
    public function setAllowUnregisteredEmails($allowUnregisteredEmails)
    {
        $this->allowUnregisteredEmails = $allowUnregisteredEmails;

        return $this;
    }

    /**
     * Get allowUnregisteredEmails
     *
     * @return bool
     */
    public function getAllowUnregisteredEmails()
    {
        return $this->allowUnregisteredEmails;
    }

    /**
     * Set allowedEmailDomains
     *
     * @param array $allowedEmailDomains
     *
     * @return School
     */
    public function setAllowedEmailDomains($allowedEmailDomains)
    {
        $this->allowedEmailDomains = $allowedEmailDomains;

        return $this;
    }

    /**
     * Get allowedEmailDomains
     *
     * @return array
     */
    public function getAllowedEmailDomains()
    {
        return $this->allowedEmailDomains;
    }

    /**
     * Get allowedEmailDomains
     *
     * @return array
     */
    public function getAllowedEmailDomainsPretty()
    {
        $domains = array_map(function($domain) {
            return '@' . $domain;
        }, $this->allowedEmailDomains);

        $last = array_pop($domains);

        if (count($domains)) {
            return implode(', ', $domains) . ' ou ' . $last;
        }

        return $last;

    }

    /**
     * Set schoolYears
     *
     * @param array $schoolYears
     *
     * @return School
     */
    public function setSchoolYears($schoolYears)
    {
        $this->schoolYears = $schoolYears;

        return $this;
    }

    /**
     * Get schoolYears
     *
     * @return array
     */
    public function getSchoolYears()
    {
        return $this->schoolYears;
    }
}

