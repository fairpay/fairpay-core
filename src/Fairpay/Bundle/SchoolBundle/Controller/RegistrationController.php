<?php


namespace Fairpay\Bundle\SchoolBundle\Controller;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeName;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeEmailType;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeNameType;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeSlug;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeSlugType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FairpayController
{
    /**
     * @Template()
     * @param Request $request
     * @param School  $school
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step1Action(Request $request, School $school)
    {
        $form = $this->createForm(SchoolChangeEmailType::class);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->updateEmail($form->getData(), $school);

                return $this->redirectToRoute(
                    'fairpay_school_registration_email_sent',
                    array('email' => $form->getData()->email)
                );
            }
        }

        return array(
            'form' => $form->createView(),
            'school' => $school,
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param School  $school
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step2Action(Request $request, School $school)
    {
        $form = $this->createForm(SchoolChangeNameType::class, new SchoolChangeName($school));

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->updateName($form->getData(), $school);

                return $this->redirectToRoute(
                    'fairpay_school_registration_step3',
                    array('registrationToken' => $school->getRegistrationToken())
                );
            }
        }

        return array(
            'form' => $form->createView(),
            'school' => $school,
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param School  $school
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function step3Action(Request $request, School $school)
    {
        $form = $this->createForm(SchoolChangeSlugType::class, new SchoolChangeSlug($school));

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->updateSlug($form->getData(), $school);

                return $this->redirectToRoute(
                    'fairpay_school_registration_step4',
                    array('registrationToken' => $school->getRegistrationToken())
                );
            }
        }

        return array(
            'form' => $form->createView(),
            'school' => $school,
        );
    }
}