<?php


namespace Fairpay\Bundle\StudentBundle\Controller;


use Fairpay\Bundle\StudentBundle\Form\StudentAddType;
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
        $form = $this->createForm(StudentAddType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->create($form->getData());

            //return $this->redirectToRoute('fairpay_student_add');
        }

        return array(
            'form' => $form->createView(),
        );
    }
}