<?php


namespace Fairpay\Bundle\StudentBundle\Manager;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Form\StudentData;
use Fairpay\Bundle\StudentBundle\Repository\StudentRepository;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;
use Fairpay\Util\Manager\NoCurrentSchoolException;

/**
 * @method StudentRepository getRepo()
 */
class StudentManager extends CurrentSchoolAwareManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayStudentBundle:Student';

    /**
     * Create a student and save it to DB.
     *
     * @param StudentData $studentAdd
     * @return Student
     * @throws NoCurrentSchoolException
     */
    public function create(StudentData $studentAdd)
    {
        $student = new Student();
        $this->updateUntouchableFields($student, $studentAdd);
        $this->mapData($student, $studentAdd);

        $student->setSchool($this->getCurrentSchool());
        $student->setIsSub(false);
        $student->setSelfRegistered(false);

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }

    /**
     * Update a student and save it to DB.
     *
     * @param Student     $student
     * @param StudentData $studentAdd
     */
    public function update(Student $student, StudentData $studentAdd)
    {
        $this->updateUntouchableFields($student, $studentAdd);
        $this->mapData($student, $studentAdd);

        $this->em->persist($student);
        $this->em->flush();
    }

    /**
     * Update $student's untouched fields based on diff with $updateFields.
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

    /**
     * @param $id
     * @return Student|null
     * @throws NoCurrentSchoolException
     */
    public function findStudentById($id)
    {
        return $this->getRepo()->findOneBy(array(
            'school' => $this->getCurrentSchool(),
            'id' => $id
        ));
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}