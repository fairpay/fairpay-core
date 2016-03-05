<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends FairpayController
{
    /**
     * @Template()
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $csrfToken = $csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

        return array(
            'error' => $error,
            'last_username' => $lastUsername,
            'csrf_token' => $csrfToken,
        );
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}