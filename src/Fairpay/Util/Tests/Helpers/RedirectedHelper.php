<?php


namespace Fairpay\Util\Tests\Helpers;

/**
 * @method showcase()
 * @method dashboard()
 * @method registrationEmailSent($email)
 * @method registrationStep1($registrationToken)
 * @method registrationStep3($registrationToken)
 * @method registrationStep4($registrationToken)
 * @method registrationStep5($registrationToken)
 * @method registrationStep6($registrationToken)
 * @method userRegistrationStep2($token)
 * @method userRegistrationStep3($token)
 * @method login()
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

        $expected = $this->normalize($expected);
        $actual   = $this->normalize($actual);

        $this->testCase->assertEquals($expected, $actual, 'You are not redirected to the right place.');
    }

    protected function normalize($path)
    {
        return preg_replace('#^//[^.]+\.localhost/#', '/', $path);
    }
}