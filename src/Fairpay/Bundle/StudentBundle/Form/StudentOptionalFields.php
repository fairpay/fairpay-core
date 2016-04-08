<?php


namespace Fairpay\Bundle\StudentBundle\Form;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Util\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="phone", entity="FairpayStudentBundle:Student")
 */
class StudentOptionalFields
{
    public $id;
    public $untouchableFields;

    public $gender;

    public $birthday;

    public $phone;

    public function __construct(Student $student = null)
    {
        if ($student) {
            foreach (get_object_vars($this) as $field => $value) {
                $this->$field = $student->{'get' . ucfirst($field)}();
            }
        }
    }
}