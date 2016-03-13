<?php


namespace Fairpay\Bundle\UserBundle\Twig;


use Fairpay\Bundle\UserBundle\Security\JwtGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ApiTokenExtension extends \Twig_Extension
{
    /** @var  JwtGenerator */
    private $jwtGenerator;

    /** @var  TokenStorage */
    private $tokenStorage;

    /**
     * ApiTokenExtension constructor.
     * @param JwtGenerator $jwtGenerator
     */
    public function __construct(JwtGenerator $jwtGenerator, TokenStorage $tokenStorage)
    {
        $this->jwtGenerator = $jwtGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('api_token', array($this, 'apiToken')),
        );
    }

    /**
     * Generate a 30 minutes token.
     * @return string
     * @throws \Fairpay\Util\Manager\NoCurrentSchoolException
     */
    public function apiToken()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        if (!is_object($user = $token->getUser())) {
            return '';
        }

        return $this->jwtGenerator->generate($user, '30 minutes');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'api_token_extension';
    }
}