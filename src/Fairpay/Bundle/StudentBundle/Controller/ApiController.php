<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Util\Controller\FairpayApiController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FairpayApiController
{
    public function studentsAction(Request $request)
    {
        $paginator = $this->get('fairpay.paginator');
        $queryAll = $this->em()->getRepository('FairpayStudentBundle:Student')->queryAll($this->get('school_manager')->getCurrentSchool());
        $students = $paginator->paginate($queryAll, $request);
        return $students;
    }

    public function showAction(Student $student)
    {
        return $student;
    }
}