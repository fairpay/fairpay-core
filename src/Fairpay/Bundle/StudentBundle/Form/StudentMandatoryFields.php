<?php


namespace Fairpay\Bundle\StudentBundle\Form;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\Validator\Constraints as Assert;

class StudentMandatoryFields
{
    public $id;
    public $untouchableFields;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     */
    public $firstName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     */
    public $lastName;

    /**
     * @Assert\NotBlank()
     */
    public $schoolYear;

    public function __construct(Student $student = null)
    {
        if ($student) {
            foreach (get_object_vars($this) as $field => $value) {
                $this->$field = $student->{'get' . ucfirst($field)}();
            }
        }
    }
}