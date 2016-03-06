<?php


namespace Fairpay\Util\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;

abstract class CurrentSchoolAwareManager extends EntityManager
{
    /** @var  SchoolManager */
    protected $schoolManager;

    /**
     * This method should be part of the `calls` service declaration.
     * @param SchoolManager $schoolManager
     */
    public function setSchoolManager(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
    }

    /**
     * @return School
     * @throws NoCurrentSchoolException
     */
    protected function getCurrentSchool()
    {
        $school = $this->schoolManager->getCurrentSchool();

        if (null === $school) {
            throw new NoCurrentSchoolException('Impossible to perform action, no current School is defined.');
        }

        return $school;
    }
}