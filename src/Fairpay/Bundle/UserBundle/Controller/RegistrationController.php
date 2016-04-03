<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Bundle\UserBundle\Entity\Token;
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

        return array(
            'user' => $token->getUser(),
        );
    }
}