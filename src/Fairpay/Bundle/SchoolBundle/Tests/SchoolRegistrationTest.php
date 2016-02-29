<?php


namespace Fairpay\Bundle\SchoolBundle\Tests;

use Fairpay\Util\Tests\WebTestCase;

class SchoolRegistrationTest extends WebTestCase
{
    public function testRegistration()
    {
        // Register from homepage
        $this->client->request('GET', $this->url->showcase());
        $this->mail->catchMails();
        $this->fillForm->schoolCreation();
        $registrationToken = $this->getRegistrationTokenFromMail();
        $this->client->followRedirect();

        // Follow link in mail
        $crawler = $this->client->request('GET', $this->url->registrationStep1($registrationToken));
        $link = $crawler->selectLink('Oui, c\'est bien ça')->link();
        $this->client->click($link);
    }

    protected function getRegistrationTokenFromMail()
    {
        return $this->mail->getLinkParam('fairpay_school_registration_step1', 'registrationToken');
    }
}