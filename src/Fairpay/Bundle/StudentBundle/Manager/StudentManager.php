<?php


namespace Fairpay\Bundle\StudentBundle\Manager;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Entity\SubHistory;
use Fairpay\Bundle\StudentBundle\Event\StudentEvent;
use Fairpay\Bundle\StudentBundle\Form\StudentData;
use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFields;
use Fairpay\Bundle\StudentBundle\Repository\StudentRepository;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;
use Fairpay\Util\Manager\NoCurrentSchoolException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @method StudentRepository getRepo()
 */
class StudentManager extends CurrentSchoolAwareManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayStudentBundle:Student';

    /** @var  TokenStorage */
    private $tokenStorage;

    /**
     * StudentManager constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

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
        $student->setSelfRegistered(false);

        $this->em->persist($student);
        $this->em->flush();

        $this->updateSubHistory($student, false);

        $this->dispatcher->dispatch(StudentEvent::onStudentCreated, new StudentEvent($student));

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
        $wasSub = $student->getIsSub();
        $this->updateUntouchableFields($student, $studentAdd);
        $this->mapData($student, $studentAdd);

        $this->em->persist($student);
        $this->em->flush();

        $this->updateSubHistory($student, $wasSub);
    }

    /**
     * @param Student                $student
     * @param StudentMandatoryFields $data
     */
    public function selfUpdate(Student $student, $data)
    {
        $this->mapData($student, $data);

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
     * Check if $student->getIsSub() has changed and create a SubHistory.
     *
     * @param Student $student
     * @param bool    $wasSub
     */
    private function updateSubHistory(Student $student, $wasSub)
    {
        if ($wasSub === $student->getIsSub()) {
            return;
        }

        $subHistory = new SubHistory($student->getIsSub(), $student, $this->getUser());

        $this->em->persist($subHistory);
        $this->em->flush();
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

    /**
     * Get the current user.
     *
     * @return User
     */
    private function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
}