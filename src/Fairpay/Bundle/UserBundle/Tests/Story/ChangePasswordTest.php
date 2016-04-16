<?php


namespace Fairpay\Bundle\UserBundle\Tests\Story;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Tests\WebTestCase;

class ChangePasswordTest extends WebTestCase
{
    private $school;
    private $token;

    public function testForgotPassword()
    {
        $this->havingASchool();
        $this->havingAUser();
        $this->requestResetPassword('batman@edu.esiee.fr');
        $this->resetPassword('b4tman_l0ve');
        $this->logout();
        $this->realLogin('bruce.wayne', 'b4tman_l0ve');
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

    private function havingAUser()
    {
        $user = new User('Bruce', 'Wayne');
        $user->setEmail('batman@edu.esiee.fr');
        $user->setSchool($this->school);
        $user->setUsername('bruce.wayne');
        $user->setDisplayName('Bruce Wayne');
        $user->setPassword('b4tman{salt}');
        $user->setSalt('salt');
        $user->setIsVendor(false);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    private function requestResetPassword($email)
    {
        $this->client->request('GET', $this->url->requestResetPassword());
        $this->mail->catchMails();
        $this->fillForm->userEmail($email);
        $this->token = $this->getTokenFromMail();
    }

    private function getTokenFromMail()
    {
        return $this->mail->getLinkParam('fairpay_user_account_reset_password', 'token', 'esiee');
    }

    private function resetPassword($plainPassword)
    {
        $this->client->request('GET', $this->url->resetPassword($this->token));
        $this->fillForm->userResetPassword($plainPassword, $plainPassword);
        $this->redirected->dashboard();
    }

    protected function logout()
    {
        $this->client->request('GET', $this->url->logout());
        $this->redirected->dashboard();
    }

    protected function realLogin($username, $password)
    {
        $this->client->request('GET', $this->url->login());
        $this->fillForm->login($username, $password);
        $this->redirected->dashboard();
    }
}