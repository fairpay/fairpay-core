<?php


namespace Fairpay\Bundle\SchoolBundle\Tests\Story;

use Fairpay\Util\Tests\WebTestCase;

class SchoolRegistrationTest extends WebTestCase
{
    protected $registrationToken;

    public function testRegistrationSuccess()
    {
        $this->registerFromHomepage();
        $this->completeStep1();
        $this->completeStep2();
        $this->completeStep3();
        $this->completeStep4();
    }

    /**
     * Register from the homepage and get the registration token from the email.
     * @param null|string $name
     * @param null|string $email
     * @return string registration token
     */
    protected function registerFromHomepage($name = 'ESIEE Paris', $email = 'bde@edu.esiee.fr')
    {
        $this->client->request('GET', $this->url->showcase());
        $this->mail->catchMails();
        $this->fillForm->schoolCreation($name, $email);
        $this->registrationToken = $this->getRegistrationTokenFromMail();
        $this->client->followRedirect();

        return $this->registrationToken;
    }

    /**
     * Go to registration step 1 and click on link to step 2.
     * @param $registrationToken
     */
    protected function completeStep1($registrationToken = null)
    {
        if (null === $registrationToken) {
            $registrationToken = $this->registrationToken;
        }

        $crawler = $this->client->request('GET', $this->url->registrationStep1($registrationToken));
        $link = $crawler->selectLink('Oui, c\'est bien ça')->link();
        $this->client->click($link);
    }

    /**
     * Send the form and go to step 3.
     * @param string|null $schoolName
     */
    protected function completeStep2($schoolName = null)
    {
        $this->fillForm->registrationStep2($schoolName);
        $this->client->followRedirect();
    }

    /**
     * Send the form and go to step 4.
     * @param string $schoolSlug
     */
    protected function completeStep3($schoolSlug = 'esiee')
    {
        $this->fillForm->registrationStep3($schoolSlug);
        $this->client->followRedirect();
    }

    /**
     * Send the form and go to step 4.
     * @param bool|null  $allowUnregisteredEmails
     * @param string|null $allowedEmailDomains
     * @internal param string $schoolSlug
     */
    protected function completeStep4($allowUnregisteredEmails = null, $allowedEmailDomains = null)
    {
        $this->fillForm->registrationStep4($allowUnregisteredEmails, $allowedEmailDomains);
        $this->client->followRedirect();
    }

    protected function getRegistrationTokenFromMail()
    {
        return $this->mail->getLinkParam('fairpay_school_registration_step1', 'registrationToken');
    }
}