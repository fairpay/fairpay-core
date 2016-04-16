<?php


namespace Fairpay\Util\Tests\Helpers;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * @method string createUserFromStudent($id)
 * @method string dashboard()
 * @method string login()
 * @method string logout()
 * @method string requestResetPassword()
 * @method string resetPassword($token)
 * @method string schoolRegistrationEmailSent($email)
 * @method string schoolRegistrationStep1($registrationToken)
 * @method string schoolRegistrationStep3($registrationToken)
 * @method string schoolRegistrationStep4($registrationToken)
 * @method string schoolRegistrationStep5($registrationToken)
 * @method string schoolRegistrationStep6($registrationToken)
 * @method string showcase()
 * @method string userRegister()
 * @method string userRegistrationStep1($token)
 * @method string userRegistrationStep2($token)
 * @method string userRegistrationStep3($token)
 */
class UrlHelper extends TestCaseHelper
{
    static public $routes = array(
        'createUserFromStudent' => ['fairpay_student_create_user', 'id'],
        'dashboard' => 'fairpay_dashboard',
        'login' => 'fairpay_login',
        'logout' => 'fairpay_logout',
        'requestResetPassword' => 'fairpay_user_account_request_reset_password',
        'resetPassword' => ['fairpay_user_account_reset_password', 'token'],
        'schoolRegistrationEmailSent' => ['fairpay_school_registration_email_sent', 'email'],
        'schoolRegistrationStep1' => ['fairpay_school_registration_step1', 'registrationToken'],
        'schoolRegistrationStep3' => ['fairpay_school_registration_step3', 'registrationToken'],
        'schoolRegistrationStep4' => ['fairpay_school_registration_step4', 'registrationToken'],
        'schoolRegistrationStep5' => ['fairpay_school_registration_step5', 'registrationToken'],
        'schoolRegistrationStep6' => ['fairpay_school_registration_step6', 'registrationToken'],
        'showcase' => 'fairpay_homepage',
        'userRegister' => 'fairpay_user_register',
        'userRegistrationStep1' => ['fairpay_user_registration_step1', 'token'],
        'userRegistrationStep2' => ['fairpay_user_registration_step2', 'token'],
        'userRegistrationStep3' => ['fairpay_user_registration_step3', 'token'],
    );

    private $subdomain;

    /**
     * Shortcut for generate.
     *
     * @param $name
     * @param $arguments
     * @return string
     */
    public function __call($name, $arguments)
    {
        return $this->generate($name, count($arguments) ? $arguments[0] : array());
    }

    /**
     * Generate the relative path for a route.
     *
     * @param string $name
     * @param array|string $parameters
     * @return string
     */
    public function generate($name, $parameters = array())
    {
        /** @var Router $router */
        $router = $this->get('router');

        $route = $name;
        if (array_key_exists($name, self::$routes)) {
            $route = self::$routes[$name];

            // $route[1] is the unique parameter name and $route[0] is the actual route name
            if (is_array($route)) {
                $parameters = array(
                    $route[1] => $parameters,
                );
                $route = $route[0];
            }
        }

        if ($this->subdomain) {
            $parameters['_subdomain'] = $this->subdomain;
            return $router->generate($route, $parameters, Router::RELATIVE_PATH);
        }

        return '/' . $router->generate($route, $parameters, Router::RELATIVE_PATH);
    }

    /**
     * @param mixed $subdomain
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;
    }
}