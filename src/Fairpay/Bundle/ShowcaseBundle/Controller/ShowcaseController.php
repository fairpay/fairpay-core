<?php

namespace Fairpay\Bundle\ShowcaseBundle\Controller;

use Fairpay\Bundle\SchoolBundle\Form\SchoolCreationType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ShowcaseController extends FairpayController
{
    /**
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SchoolCreationType::class);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->create($form->getData());

                return $this->redirectToRoute(
                    'fairpay_school_registration_email_sent',
                    array('email' => $form->getData()->email)
                );
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
