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