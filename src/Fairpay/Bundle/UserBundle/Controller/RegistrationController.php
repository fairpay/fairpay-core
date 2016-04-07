<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFields;
use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFieldsType;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFields;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFieldsType;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Form\UserSetPasswordType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FairpayController
{
    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step1Action(Request $request, Token $token)
    {
        $student = $token->getUser()->getStudent();
        $form    = $this->createForm(StudentMandatoryFieldsType::class, new StudentMandatoryFields($student));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->selfUpdate($student, $form->getData());

            return $this->redirectToRoute(
                'fairpay_user_registration_step2',
                array('token' => $token)
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step2Action(Request $request, Token $token)
    {
        $student = $token->getUser()->getStudent();
        $form    = $this->createForm(StudentOptionalFieldsType::class, new StudentOptionalFields($student));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->selfUpdate($student, $form->getData());

            return $this->redirectToRoute(
                'fairpay_user_registration_step3',
                array('token' => $token)
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step3Action(Request $request, Token $token)
    {
        $form = $this->createForm(UserSetPasswordType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('user_manager')->setPassword($token->getUser(), $form->getData());

            return $this->redirectToRoute(
                'fairpay_user_registration_step3',
                array('token' => $token)
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }
}