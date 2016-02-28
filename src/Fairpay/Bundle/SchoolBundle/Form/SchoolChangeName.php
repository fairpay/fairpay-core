<?php


namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Symfony\Component\Validator\Constraints as Assert;
use Fairpay\Util\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields="name", entity="FairpaySchoolBundle:School")
 */
class SchoolChangeName
{
    public $id;

    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * SchoolChangeName constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->id = $school->getId();
        $this->name = $school->getName();
    }
}