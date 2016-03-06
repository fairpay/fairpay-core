<?php


namespace Fairpay\Bundle\StudentBundle\Manager;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Form\StudentAdd;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;
use Fairpay\Util\Manager\NoCurrentSchoolException;

class StudentManager extends CurrentSchoolAwareManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayStudentBundle:Student';

    /**
     * Create a student and save it to DB.
     *
     * @param StudentAdd $studentAdd
     * @throws NoCurrentSchoolException
     */
    public function create(StudentAdd $studentAdd)
    {
        $student = new Student();
        $this->updateUntouchableFields($student, $studentAdd);
        $this->mapData($student, $studentAdd);

        $student->setSchool($this->getCurrentSchool());
        $student->setIsSub(false);
        $student->setSelfRegistered(false);

        $this->em->persist($student);
        $this->em->flush();
    }

    /**
     * Update $student's untouche fields based on diff with $updateFields.
     * @param Student $student
     * @param object  $updateFields
     */
    private function updateUntouchableFields(Student $student, $updateFields)
    {
        $untouchableFields = $student->getUntouchableFields();

        foreach (get_object_vars($updateFields) as $field => $value) {
            if (null === $value) {
                if(($key = array_search($field, $untouchableFields)) !== false) {
                    unset($untouchableFields[$key]);
                }
            } else {
                if ($student->{'get' . ucfirst($field)}() != $value) {
                    $untouchableFields[] = $field;
                }
            }
        }

        $student->setUntouchableFields(array_unique($untouchableFields));
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}