<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PublicController extends FairpayController
{
    /**
     * @Template()
     * @param Student $student
     * @return array
     */
    public function profileAction(Student $student)
    {
        return array(
            'student' => $this->get('jms_serializer')->toArray($student),
        );
    }
}