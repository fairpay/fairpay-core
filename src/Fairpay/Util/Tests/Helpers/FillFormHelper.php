<?php


namespace Fairpay\Util\Tests\Helpers;

/**
 * Fill and submit a form with default valid data. Default data can be overwritten.
 */
class FillFormHelper extends TestCaseHelper
{
    public function schoolCreation($name = 'ESIEE Paris', $email = 'bde@edu.esiee.fr')
    {
        $this->sendForm('school_create', array(
            'name' => $name,
            'email' => $email,
        ));
    }

    public function registrationChangeEmail($email = 'bde@edu.esiee.fr')
    {
        $this->sendForm('school_change_email', array(
            'email' => $email,
        ));
    }

    public function registrationStep2($schoolName = null)
    {
        $this->sendForm('school_change_name', array(
            'name' => $schoolName,
        ));
    }

    public function registrationStep3($schoolSlug = 'esiee')
    {
        $this->sendForm('school_change_slug', array(
            'slug' => $schoolSlug,
        ));
    }

    public function registrationStep4($allowUnregisteredEmails = null, $allowedEmailDomains = null)
    {
        $this->sendForm('school_email_policy', array(
            'allowUnregisteredEmails' => $allowUnregisteredEmails,
            'allowedEmailDomains' => $allowedEmailDomains,
        ));
    }

    public function registrationStep5($username = 'Ferus')
    {
        $this->sendForm('school_choose_username', array(
            'username' => $username,
        ));
    }

    public function registrationStep6($plainPassword = 'Hell0!')
    {
        $this->sendForm('school_choose_password', array(
            'plainPassword' => $plainPassword,
        ));
    }

    public function userEmail($email = null)
    {
        $this->sendForm('user_email', array(
            'email' => $email,
        ));
    }

    public function userRegistrationStep1($firstName = null, $lastName = null, $schoolYear = null)
    {
        $this->sendForm('student_mandatory_fields', array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'schoolYear' => $schoolYear,
        ));
    }

    public function userRegistrationStep2($gender = null, $birthday = null, $phone = null)
    {
        $this->sendForm('student_optional_fields', array(
            'gender' => $gender,
            'birthday' => $birthday,
            'phone' => $phone,
        ));
    }

    public function userRegistrationStep3($plainPassword = null)
    {
        $this->sendForm('user_set_password', array(
            'plainPassword' => $plainPassword,
        ));
    }

    /**
     * Get a form from its name.
     *
     * @param string $name
     * @return \Symfony\Component\DomCrawler\Form
     */
    protected function getForm($name)
    {
        return $this->getCrawler()->filter("form[name=$name]")->form();
    }

    protected function sendForm($name, $data)
    {
        $form = $this->getForm($name);
        $formData = array();
        foreach($data as $key => $value) {
            $formData[$name."[$key]"] = $value === null ? $this->getFormValue($form, $name, $key) : $value;
        }
        $this->getClient()->submit($form, $formData);
    }

    protected function getFormValue($form, $name, $field)
    {
        $values = $form->getValues();
        $key = $name."[$field]";

        if (isset($values[$key])) {
            return $values[$name."[$field]"];
        }

        return null;
    }
}