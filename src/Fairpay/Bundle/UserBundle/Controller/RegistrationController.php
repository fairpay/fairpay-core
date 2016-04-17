<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFields;
use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFieldsType;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFields;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFieldsType;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Exception\NotAllowedEmailDomainException;
use Fairpay\Bundle\UserBundle\Exception\UnregisteredEmailsNotAllowedException;
use Fairpay\Bundle\UserBundle\Form\UserEmailType;
use Fairpay\Bundle\UserBundle\Form\UserSetPasswordType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FairpayController
{
    /**
     * @Template()
     * @param Request $request
     * @return array
     */
    public function registerAction(Request $request)
    {
        $form  = $this->createForm(UserEmailType::class);
        $email = $this->get('session')->get('registration_attempt_email');

        if ($email || $request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $email = $email ? $email : $form->getData()->email;
            $user = $this->get('user_manager')->findUserByUsernameOrEmail($email);

            if ($user) {
                return $this->handleAlreadyRegisteredUser($user);
            } else {
                try {
                    $this->get('user_manager')->createFromEmail($email);
                    $this->flashSuccess('Un email vous a été envoyé pour finaliser votre inscription.');
                } catch (NotAllowedEmailDomainException $e) {
                    $this->flashError(sprintf(
                        'Vous devez utiliser votre adresse email scolaire pour vous inscrire: %s.',
                        $this->get('school_manager')->getCurrentSchool()->getAllowedEmailDomainsPretty()
                    ));
                } catch (UnregisteredEmailsNotAllowedException $e) {
                    $this->flashError(sprintf(
                        'Votre adresse email n\'est pas sur la liste des élèves, demandez au BDE (%s) de vous ajouter .',
                        $this->get('school_manager')->getCurrentSchool()->getEmail()
                    ));
                }
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * Handle users that try to register with the sam email.
     *
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function handleAlreadyRegisteredUser(User $user)
    {
        $session = $this->get('session');

        if ($confirmed = $session->has('registration_attempt_email')) {
            $session->remove('registration_attempt_email');
        } else {
            $session->set('registration_attempt_email', $user->getEmail());
        }

        // User did not finish registration flow
        if ($this->get('token_manager')->hasToken($user, Token::REGISTER)) {
            $template = '@FairpayUser/Registration/resend_registration_email.html.twig';

            if ($confirmed) {
                $this->get('user_manager')->requestResendRegistrationEmail($user);
            }

        // User just wants a new password
        } else {
            $template = '@FairpayUser/Registration/request_reset_password.html.twig';

            if ($confirmed) {
                $this->get('user_manager')->requestResetPassword($user);
            }
        }

        return $this->render($template, array(
            'confirmed' => $confirmed,
        ));
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step1Action(Request $request, Token $token)
    {
        $user    = $token->getUser();
        $student = $user->getStudent();
        $form    = $this->createForm(StudentMandatoryFieldsType::class, new StudentMandatoryFields($student));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->selfUpdate($student, $form->getData());

            return $this->redirectToRoute(
                'fairpay_user_registration_step2',
                array('token' => $token)
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step2Action(Request $request, Token $token)
    {
        $student = $token->getUser()->getStudent();
        $form    = $this->createForm(StudentOptionalFieldsType::class, new StudentOptionalFields($student));

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $this->get('student_manager')->selfUpdate($student, $form->getData());

            return $this->redirectToRoute(
                'fairpay_user_registration_step3',
                array('token' => $token)
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step3Action(Request $request, Token $token)
    {
        $form = $this->createForm(UserSetPasswordType::class);
        $user = $token->getUser();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $userManager = $this->get('user_manager');
            $userManager->setPassword($user, $form->getData());
            $userManager->login($user);
            $this->get('token_manager')->remove($token);

            $this->flashSuccess('Félicitation, vous êtes maintenant inscrit sur Fairpay !');

            return $this->redirectToRoute(
                'fairpay_dashboard'
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }
}