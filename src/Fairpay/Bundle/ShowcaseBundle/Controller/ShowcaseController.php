<?php

namespace Fairpay\Bundle\ShowcaseBundle\Controller;

use Fairpay\Bundle\SchoolBundle\Form\SchoolRegisterType;
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
        $form = $this->createForm(SchoolRegisterType::class);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->register($form->getData());
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
