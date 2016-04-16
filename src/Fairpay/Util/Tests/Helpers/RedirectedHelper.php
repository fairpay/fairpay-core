<?php


namespace Fairpay\Util\Tests\Helpers;

/**
 * @method createUserFromStudent($id)
 * @method dashboard()
 * @method login()
 * @method logout()
 * @method requestResetPassword()
 * @method resetPassword($token)
 * @method schoolRegistrationEmailSent($email)
 * @method schoolRegistrationStep1($registrationToken)
 * @method schoolRegistrationStep3($registrationToken)
 * @method schoolRegistrationStep4($registrationToken)
 * @method schoolRegistrationStep5($registrationToken)
 * @method schoolRegistrationStep6($registrationToken)
 * @method showcase()
 * @method userRegister()
 * @method userRegistrationStep1($token)
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

        $expected = $this->normalize($expected);
        $actual   = $this->normalize($actual);

        $this->testCase->assertEquals($expected, $actual, 'You are not redirected to the right place.');
    }

    protected function normalize($path)
    {
        return preg_replace('#^//[^.]+\.localhost/#', '/', $path);
    }
}