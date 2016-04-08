<?php


namespace Fairpay\Util\Tests\Helpers;

/**
 * @method showcase()
 * @method registrationEmailSent($email)
 * @method registrationStep1($registrationToken)
 * @method registrationStep3($registrationToken)
 * @method registrationStep4($registrationToken)
 * @method registrationStep5($registrationToken)
 * @method registrationStep6($registrationToken)
 * @method userRegistrationStep2($token)
 * @method userRegistrationStep3($token)
 */
class RedirectedHelper extends TestCaseHelper
{
    /**
     * Shortcut for generate.
     *
     * @param $name
     * @param $arguments
     * @return string
     */
    public function __call($name, $arguments)
    {
        $this->testCase->client->followRedirect();

        $expected = $this->testCase->url->__call($name, $arguments);
        $actual = $this->testCase->client->getRequest()->getRequestUri();

        $this->testCase->assertEquals($this->normalize($expected), $this->normalize($actual));
    }

    protected function normalize($path)
    {
        return preg_replace('#^//[^.]+\.localhost/#', '/', $path);
    }
}