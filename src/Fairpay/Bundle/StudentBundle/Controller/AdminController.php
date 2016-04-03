<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Form\StudentData;
use Fairpay\Bundle\StudentBundle\Form\StudentDataType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends FairpayController
{
    /**
     * @Template()
     */
    public function listAction()
    {
        return array(
            'token' => $this->get('jwt_generator')->generate($this->getUser()),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Request $request)
    {
        $form = $this->createForm(StudentDataType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $student = $this->get('student_manager')->create($form->getData());

            return $this->redirectToRoute('fairpay_profile_student', ['id' => $student->getId()]);
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Student $student
     * @return array
     */
    public function editAction(Request $request, Student $student)
    {
        $form = $this->createForm(StudentDataType::class, new StudentData($student));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->update($student, $form->getData());

            return $this->redirectToRoute('fairpay_profile_student', ['id' => $student->getId()]);
        }

        return array(
            'student' => $student,
            'form' => $form->createView(),
        );
    }

    public function createUserAction(Student $student)
    {
        if ($student->hasAccount()) {
            // TODO add error message
        } else {
            $this->get('user_manager')->createFromStudent($student);
        }

        return $this->redirectToRoute('fairpay_profile_student', ['id' => $student->getId()]);
    }
}