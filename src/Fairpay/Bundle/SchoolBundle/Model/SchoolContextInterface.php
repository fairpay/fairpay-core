<?php


namespace Fairpay\Bundle\SchoolBundle\Model;


use Fairpay\Bundle\SchoolBundle\Entity\School;

interface SchoolContextInterface
{
    /**
     * @return School|null
     */
    public function getSchool();

    /**
     * @param School $school
     * @return self
     */
    public function setSchool(School $school);
}