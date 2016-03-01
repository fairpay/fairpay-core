<?php


namespace Fairpay\Util\Tests\Helpers;

/**
 * Fill and submit a form with default valid data. Default data can be overwritten.
 */
class FillFormHelper extends TestCaseHelper
{
    public function schoolCreation(array $data = array())
    {
        $form = $this->getForm('school_creation');
        $data = array_merge(array(
            'school_creation[name]' => 'ESIEE Paris',
            'school_creation[email]' => 'bde@edu.esiee.fr',
        ), $data);

        $this->getClient()->submit($form, $data);
    }

    public function registrationStep2($schoolName = null)
    {
        $form = $this->getForm('school_change_name');
        if ($schoolName === null) {
            $schoolName = $form->getValues()['school_change_name[name]'];
        }

        $this->getClient()->submit($form, array(
            'school_change_name[name]' => $schoolName,
        ));
    }

    public function registrationStep3($schoolSlug = 'esiee')
    {
        $form = $this->getForm('school_change_slug');
        $this->getClient()->submit($form, array(
            'school_change_slug[slug]' => $schoolSlug,
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
}