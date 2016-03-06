<?php


namespace Fairpay\Util\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;

class FairpayApiController extends FairpayController
{
    protected function view($data)
    {
        $serializer = $this->get('jms_serializer');

        return new JsonResponse($serializer->toArray($data));
    }
}