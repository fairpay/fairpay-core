<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Form\StudentData;
use Fairpay\Bundle\StudentBundle\Form\StudentDataType;
use Fairpay\Bundle\UserBundle\Annotation\Permission;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Permission("STUDENTS_MANAGE")
 */
class AdminController extends FairpayController
{
    /**
     * @Template()
     */
    public function listAction()
    {
        return array();
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

            $this->flashSuccess(sprintf('%s à bien été créé.', $student));

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

            $this->flashSuccess(sprintf('%s à bien été mis à jour.', $student));

            return $this->redirectToRoute('fairpay_profile_student', ['id' => $student->getId()]);
        }

        return array(
            'student' => $student,
            'form' => $form->createView(),
        );
    }

    /**
     * @Permission("ACCOUNTS_MANAGE")
     * @param Student $student
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createUserAction(Student $student)
    {
        if ($student->hasAccount()) {
            $this->flashError(sprintf('%s a déjà un compte.', $student));
        } else {
            $this->get('user_manager')->createFromStudent($student);
            $this->flashSuccess(sprintf('Un email a été envoyé à %s pour finaliser son inscription.', $student));
        }

        return $this->redirectToRoute('fairpay_profile_student', ['id' => $student->getId()]);
    }
}