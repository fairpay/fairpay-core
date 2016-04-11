<?php


namespace Fairpay\Bundle\UserBundle\Controller;


use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFields;
use Fairpay\Bundle\StudentBundle\Form\StudentMandatoryFieldsType;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFields;
use Fairpay\Bundle\StudentBundle\Form\StudentOptionalFieldsType;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
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
        $form = $this->createForm(UserEmailType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $email = $form->getData()->email;
            $user = $this->get('user_manager')->findUserByUsernameOrEmail($email);

            // User with this email already exists
            if ($user) {
                if ($this->get('token_manager')->hasToken($user, Token::REGISTER)) {
                    $template = '@FairpayUser/Registration/resend_registration_email.html.twig';
                } else {
                    $template = '@FairpayUser/Registration/request_reset_password.html.twig';
                }

                return $this->render($template, array(
                    'email' => $email,
                ));

            // User does not exist yet
            } else {
                $student = $this->get('student_manager')->findStudentByEmail($email);

                if (!$student) {
                    $school = $this->get('school_manager')->getCurrentSchool();
                    $domain = $this->get('fairpay.email_helper')->getDomain($email);

                    if (!$school->getAllowUnregisteredEmails()) {
                        $this->flashError(sprintf(
                            'Votre adresse email n\'est pas sur la liste des élèves, demandez au BDE (%s) de vous ajouter.',
                            $school->getEmail()
                        ));

                        return array(
                            'form' => $form->createView(),
                        );
                    }

                    if (!in_array($domain, $school->getAllowedEmailDomains())) {
                        $this->flashError(sprintf(
                            'Vous devez utiliser votre adresse email scolaire pour vous inscrire: %s.',
                            $school->getAllowedEmailDomainsPretty()
                        ));

                        return array(
                            'form' => $form->createView(),
                        );
                    }

                    $student = $this->get('student_manager')->createBlank($email);
                }

                $this->get('user_manager')->createFromStudent($student, UserCreatedEvent::SELF_REGISTERED);
                $this->flashSuccess('Un email vous a été envoyé pour finaliser votre inscription.');
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Token   $token
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step1Action(Request $request, Token $token)
    {
        $student = $token->getUser()->getStudent();
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
                'fairpay_student_list'
            );
        }

        return array(
            'form' => $form->createView(),
            'user' => $token->getUser(),
        );
    }
}