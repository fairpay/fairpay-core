<?php


namespace Fairpay\Bundle\UserBundle\Tests\Story;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Util\Tests\WebTestCase;

class UserRegistrationTest extends WebTestCase
{
    private $school;
    private $token;

    public function testRegistrationByAdmin()
    {
        $this->havingASchool();
        $this->adminCreatesStudentAndRegistersUser();
        $this->completeStep1();
        $this->completeStep2();
        $this->completeStep3();
    }

    private function havingASchool()
    {
        $this->school = new School('ESIEE Paris', 'bde@edu.esiee.fr');
        $this->school->setSlug('esiee');

        $this->em->persist($this->school);
        $this->em->flush();

        $this->url->setSubdomain('esiee');
    }

    private function havingAStudent()
    {
        $student = new Student('Bruce', 'Wayne', 'batman@gmail.com');
        $student->setSchoolYear('E5');
        $student->setSchool($this->school);
        $student->setSelfRegistered(false);
        $student->setUntouchableFields(['firstName', 'lastName', 'email', 'schoolYear']);

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }

    private function adminCreatesStudentAndRegistersUser()
    {
        $student = $this->havingAStudent();
        $this->mail->catchMails();

        $this->login();
        $this->client->request('GET', $this->url->createUserFromStudent($student->getId()));
        $this->logout();

        $this->token = $this->getTokenFromMail();
    }

    private function getTokenFromMail()
    {
        return $this->mail->getLinkParam('fairpay_user_registration_step1', 'token', 'esiee');
    }

    private function completeStep1($token = null)
    {
        if (null === $token) {
            $token = $this->token;
        }

        $this->client->request('GET', $this->url->userRegistrationStep1($token));
        $this->fillForm->userRegistrationStep1();
        $this->redirected->userRegistrationStep2($token);
    }

    private function completeStep2()
    {
        $this->fillForm->userRegistrationStep2();
        $this->redirected->userRegistrationStep3($this->token);
    }

    private function completeStep3()
    {
        $this->fillForm->userRegistrationStep3();
    }
}