<?php


namespace Fairpay\Bundle\StudentBundle\Form;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\Validator\Constraints as Assert;

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