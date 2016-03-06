<?php


namespace Fairpay\Bundle\SchoolBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use JMS\Serializer\Annotation\Exclude;

abstract class SchoolContext implements SchoolContextInterface
{
    /**
     * @var School
     * @ORM\ManyToOne(targetEntity="Fairpay\Bundle\SchoolBundle\Entity\School", fetch="EXTRA_LAZY")
     * @Exclude()
     */
    protected $school;

    /**
     * @return School|null
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param School $school
     * @return self
     */
    public function setSchool(School $school)
    {
        $this->school = $school;
        return $this;
    }
}