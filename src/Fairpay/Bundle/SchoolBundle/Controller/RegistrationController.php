<?php


namespace Fairpay\Bundle\SchoolBundle\Controller;


use Doctrine\ORM\EntityManager;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Form\SchoolChangeEmailType;
use Fairpay\Util\Controller\FairpayController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FairpayController
{
    /**
     * @Template()
     */
    public function step1Action(Request $request, School $school)
    {
        $form = $this->createForm(SchoolChangeEmailType::class);

        if ($request->isMethod('POST')) {
            if ($form->handleRequest($request)->isValid()) {
                $this->get('school_manager')->updateEmail($form->getData(), $school);
            }
        }

        return array(
            'form' => $form->createView(),
            'school' => $school,
        );
    }

    /**
     * @Template()
     */
    public function step2Action(School $school)
    {
        return array(
            'school' => $school,
        );
    }
}