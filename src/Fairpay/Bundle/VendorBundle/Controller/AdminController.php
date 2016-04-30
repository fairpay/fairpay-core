<?php


namespace Fairpay\Bundle\VendorBundle\Controller;


use Fairpay\Bundle\UserBundle\Annotation\Permission;
use Fairpay\Bundle\VendorBundle\Form\VendorDataType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends FairpayController
{
    /**
     * @Template()
     * @Permission("VENDORS_VIEW")
     * @return array
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
        $form = $this->createForm(VendorDataType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $vendor = $this->get('vendor_manager')->create($form->getData());

            $this->flashSuccess(sprintf('Le marchand %s à bien été créé.', $vendor));

            return $this->redirectToRoute('fairpay_profile_vendor', ['id' => $vendor->getId()]);
        }

        return array(
            'form' => $form->createView(),
        );
    }
}