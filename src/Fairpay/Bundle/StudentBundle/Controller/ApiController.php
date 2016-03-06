<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Util\Controller\FairpayApiController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FairpayApiController
{
    public function studentsAction(Request $request)
    {
        $paginator = $this->get('fairpay.paginator');
        $queryAll = $this->em()->getRepository('FairpayStudentBundle:Student')->queryAll();
        $students = $paginator->paginate($queryAll, $request);
        return $this->view($students);
    }
}