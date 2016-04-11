<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Form\UserChangePasswordType;
use Fairpay\Bundle\UserBundle\Form\UserEmailType;
use Fairpay\Bundle\UserBundle\Form\UserResetPasswordType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends FairpayController
{
    /**
     * @Template()
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(UserChangePasswordType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('user_manager')->setPassword($this->getUser(), $form->getData());

            return $this->redirectToRoute('fairpay_user_account_change_password');
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function requestResetPasswordAction(Request $request)
    {
        $form = $this->createForm(UserEmailType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('user_manager')->requestResetPassword($form->getData()->email);

            return $this->redirectToRoute('fairpay_user_account_request_reset_password');
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array
     */
    public function resetPasswordAction(Request $request, Token $token)
    {
        $form = $this->createForm(UserResetPasswordType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('user_manager')->setPassword($token->getUser(), $form->getData());
            $this->get('user_manager')->login($token->getUser());
            $this->get('token_manager')->remove($token);

            return $this->redirectToRoute('fairpay_dashboard');
        }

        return array(
            'form' => $form->createView(),
        );
    }
}