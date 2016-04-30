<?php


namespace Fairpay\Bundle\UserBundle\Tests\Story;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder;
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
        $this->completeStep3('b4tman');

        $user = $this->em->getRepository('FairpayUserBundle:User')->findOneBy(['email' => 'batman@edu.esiee.fr']);
        $this->assertBatman($user);
    }

    public function testSelfRegistration()
    {
        $this->havingASchool();
        $this->register('batman@edu.esiee.fr');
        $this->completeStep1(null, 'Bruce', 'Wayne', 'E5');
        $this->completeStep2();
        $this->completeStep3('b4tman');

        $user = $this->em->getRepository('FairpayUserBundle:User')->findOneBy(['email' => 'batman@edu.esiee.fr']);
        $this->assertBatman($user);
    }

    public function testRegisterWithAlmostRegisteredEmail()
    {
        $this->havingASchool();
        $this->register('batman@edu.esiee.fr');
        $this->register('batman@edu.esiee.fr', false);

        $btn = $this->client->getCrawler()->filter('#resend-email')->eq(0)->link();

        $this->mail->catchMails();
        $this->client->click($btn);
        $this->assertEquals($this->token, $this->getTokenFromMail());
    }

    public function testRegisterWithAlreadyRegisteredEmail()
    {
        $this->havingASchool();
        $this->havingAUser();
        $this->register('batman@edu.esiee.fr', false);

        $btn = $this->client->getCrawler()->filter('#reset-password')->eq(0)->link();

        $this->mail->catchMails();
        $this->client->click($btn);
        return $this->mail->getLinkParam('fairpay_user_account_reset_password', 'token', 'esiee');
    }

    private function havingASchool()
    {
        $this->school = new School('ESIEE Paris', 'bde@edu.esiee.fr');
        $this->school->setSlug('esiee');
        $this->school->setAllowUnregisteredEmails(true);
        $this->school->setAllowedEmailDomains(['edu.esiee.fr']);

        $this->em->persist($this->school);
        $this->em->flush();

        $this->url->setSubdomain('esiee');
    }

    private function havingAStudent()
    {
        $student = new Student('Bruce', 'Wayne', 'batman@edu.esiee.fr');
        $student->setSchoolYear('E5');
        $student->setSchool($this->school);
        $student->setSelfRegistered(false);
        $student->setUntouchableFields(['firstName', 'lastName', 'email', 'schoolYear']);

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }

    private function havingAUser()
    {
        $user = new User('Bruce', 'Wayne');
        $user->setEmail('batman@edu.esiee.fr');
        $user->setSchool($this->school);
        $user->setUsername('bruce.wayne');
        $user->setDisplayName('Bruce Wayne');
        $user->setPassword('b4tman');
        $user->setSalt('salt');
        $user->setIsVendor(false);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function adminCreatesStudentAndRegistersUser()
    {
        $student = $this->havingAStudent();
        $this->mail->catchMails();

        $this->login(['global' => MaskBuilder::MASK_ACCOUNTS_MANAGE]);
        $this->client->request('GET', $this->url->createUserFromStudent($student->getId()));
        $this->logout();

        $this->token = $this->getTokenFromMail();
    }

    private function register($email = 'batman@edu.esiee.fr', $shouldSendEmail = true)
    {
        $this->client->request('GET', $this->url->userRegister());

        if ($shouldSendEmail) {
            $this->mail->catchMails();
        }

        $this->fillForm->userEmail($email);

        if ($shouldSendEmail) {
            $this->token = $this->getTokenFromMail();
        }
    }

    private function getTokenFromMail()
    {
        return $this->mail->getLinkParam('fairpay_user_registration_step1', 'token', 'esiee');
    }

    private function completeStep1($token = null, $firstName = null, $lastName = null, $schoolYear = null)
    {
        if (null === $token) {
            $token = $this->token;
        }

        $this->client->request('GET', $this->url->userRegistrationStep1($token));
        $this->fillForm->userRegistrationStep1($firstName, $lastName, $schoolYear);
        $this->redirected->userRegistrationStep2($token);
    }

    private function completeStep2()
    {
        $this->fillForm->userRegistrationStep2();
        $this->redirected->userRegistrationStep3($this->token);
    }

    private function completeStep3($plainPassword = null)
    {
        $this->fillForm->userRegistrationStep3($plainPassword);
        $this->redirected->dashboard();
    }

    private function assertBatman(User $user)
    {
        $this->assertNotNull($user->getId());
        $this->assertEquals('bruce.wayne', $user->getUsername());
        $this->assertEquals('Bruce Wayne', $user->getDisplayName());
        $this->assertStringStartsWith('b4tman{', $user->getPassword());
        $this->assertNotNull($user->getSalt());
        $this->assertFalse($user->getIsVendor());
        $this->assertEquals($this->school, $user->getSchool());
    }
}