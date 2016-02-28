<?php


namespace Fairpay\Bundle\SchoolBundle\Event;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Symfony\Component\EventDispatcher\Event;

class SchoolCreatedEvent extends Event
{
    /**
     * @var School
     */
    private $school;

    /**
     * SchoolCreatedEvent constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * @return School
     */
    public function getSchool()
    {
        return $this->school;
    }
}