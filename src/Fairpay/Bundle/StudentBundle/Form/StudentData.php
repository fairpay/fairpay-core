<?php


namespace Fairpay\Bundle\StudentBundle\Form;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Util\Email\Validator\Constraints\NotDisposableEmail;
use Fairpay\Util\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="email", entity="FairpayStudentBundle:Student")
 * @UniqueEntity(fields="barcode", entity="FairpayStudentBundle:Student")
 * @UniqueEntity(fields="phone", entity="FairpayStudentBundle:Student")
 */
class StudentData
{
    public $id;

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
     * @Assert\Email()
     * @NotDisposableEmail()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     */
    public $schoolYear;

    public $gender;

    public $birthday;

    public $barcode;

    public $phone;

    public $isSub = false;

    public function __construct(Student $student = null)
    {
        if ($student) {
            foreach (get_object_vars($this) as $field => $value) {
                $this->$field = $student->{'get' . ucfirst($field)}();
            }
        }
    }
}