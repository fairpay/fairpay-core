<?php


namespace Fairpay\Bundle\StudentBundle\Form;

use Fairpay\Util\Email\Validator\Constraints\NotDisposableEmail;
use Fairpay\Util\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields="email", entity="FairpayStudentBundle:Student")
 */
class StudentAdd
{
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
}