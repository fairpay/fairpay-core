<?php


namespace Fairpay\Bundle\VendorBundle\Controller;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PublicController extends FairpayController
{
    /**
     * @Template()
     * @param User $vendor
     * @return array
     */
    public function profileAction(User $vendor)
    {
        return array(
            'vendor' => $this->get('jms_serializer')->toArray($vendor),
        );
    }
}