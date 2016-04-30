<?php


namespace Fairpay\Bundle\VendorBundle\Controller;


use Fairpay\Bundle\UserBundle\Annotation\Permission;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\VendorBundle\Form\GroupAssignmentType;
use Fairpay\Bundle\VendorBundle\Form\GroupDataType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Permission("_ADMINS_MANAGE")
 */
class AdministratorsController extends FairpayController
{
    /**
     * @Template()
     * @param Request $request
     * @param User    $vendor
     * @return array
     */
    public function listAction(Request $request, User $vendor)
    {
        $admins = $this->getRepository('FairpayUserBundle:User')->findAdministrators($vendor);
        $form = $this->createForm(GroupAssignmentType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('vendor_manager')->assignGroup($vendor, $form->getData());

            $this->flashSuccess(sprintf('Les droits de %s ont bien été mis à jour.', $form->getData()->user));

            return $this->redirectToRoute('fairpay_vendor_administrators', ['id' => $vendor->getId()]);
        }

        return array(
            'vendor' => $vendor,
            'admins' => $admins,
            'form' => $form->createView(),
        );
    }

    public function removeUserAction(Request $request, User $vendor)
    {
        $this->get('vendor_manager')->removeUserFromAdmins($vendor, $request->query->get('user'));

        return $this->redirectToRoute('fairpay_vendor_administrators', ['id' => $vendor->getId()]);
    }

    /**
     * @Template()
     * @param Request $request
     * @param User    $vendor
     * @return array
     */
    public function addGroupAction(Request $request, User $vendor)
    {
        $form = $this->createForm(GroupDataType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $group = $this->get('vendor_manager')->addGroup($vendor, $form->getData());

            $this->flashSuccess(sprintf('Le groupe %s à bien été créé.', $group));

            return $this->redirectToRoute('fairpay_vendor_administrators', ['id' => $vendor->getId()]);
        }

        return array(
            'form' => $form->createView(),
            'vendor' => $vendor,
        );
    }
}