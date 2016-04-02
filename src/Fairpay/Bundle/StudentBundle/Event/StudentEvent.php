<?php


namespace Fairpay\Bundle\StudentBundle\Event;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\EventDispatcher\Event;

class StudentEvent extends Event
{
    const onStudentCreated = 'fairpay.student.created';

    /**
     * @var Student
     */
    private $student;

    /**
     * StudentEvent constructor.
     * @param Student $student
     */
    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    /**
     * @return Student
     */
    public function getStudent()
    {
        return $this->student;
    }
}