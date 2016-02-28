<?php


namespace Fairpay\Bundle\SchoolBundle\Event;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Symfony\Component\EventDispatcher\Event;

class SchoolEvent extends Event
{
    const onSchoolCreated = 'fairpay.school.created';
    const onSchoolChangedEmail = 'fairpay.school.changed_email';

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