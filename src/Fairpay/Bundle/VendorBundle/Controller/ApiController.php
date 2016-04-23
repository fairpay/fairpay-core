<?php


namespace Fairpay\Bundle\VendorBundle\Controller;


use Fairpay\Util\Controller\FairpayApiController;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends FairpayApiController
{
    public function vendorsAction(Request $request)
    {
        $paginator = $this->get('fairpay.paginator');
        $queryAll = $this->em()->getRepository('FairpayUserBundle:User')->queryAllVendors($this->get('school_manager')->getCurrentSchool());

        return $paginator->paginate($queryAll, $request);
    }
}