<?php


namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Validator\Constraints\SchoolSlug;
use Symfony\Component\Validator\Constraints as Assert;
use Fairpay\Util\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity(fields="slug", entity="FairpaySchoolBundle:School")
 */
class SchoolChangeSlug
{
    public $id;

    /**
     * @Assert\NotBlank()
     * @SchoolSlug()
     */
    public $slug;

    /**
     * SchoolChangeSlug constructor.
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->id = $school->getId();
        $this->slug = $school->getSlug();
    }
}