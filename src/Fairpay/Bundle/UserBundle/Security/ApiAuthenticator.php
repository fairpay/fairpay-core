<?php


namespace Fairpay\Bundle\UserBundle\Security;

use Doctrine\ORM\EntityManager;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Util\EventListener\ExceptionListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManager */
    private $em;

    /** @var JwtGenerator */
    private $jwtGenerator;

    /** @var UserManager */
    private $userManager;

    /** @var SchoolManager */
    private $schoolManager;

    public function __construct(EntityManager $em, JwtGenerator $jwtGenerator, UserManager $userManager, SchoolManager $schoolManager)
    {
        $this->em = $em;
        $this->jwtGenerator = $jwtGenerator;
        $this->userManager = $userManager;
        $this->schoolManager = $schoolManager;
    }

    /**
     * @param Request $request
     * @return array|mixed|null|void
     */
    public function getCredentials(Request $request)
    {
        // Can't use $request->headers->get('Authorization') because of Apache
        $token = isset(getallheaders()['Authorization']) ? getallheaders()['Authorization'] : null;

        if (!$token || substr($token, 0, 7) !== 'Bearer ') {
            throw new UnauthorizedHttpException('', 'Vous devez vous authentifier.');
        }

        return substr($token, 7);
    }

    /**
     * @param string                $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->jwtGenerator->decode($credentials);

        if (true !== $message = $this->jwtGenerator->isValid($token)) {
            throw new AuthenticationException($message, ExceptionListener::INVALID_TOKEN);
        }

        $userPayload = $token->getPayload()->findClaimByName('user')->getValue();
        $this->schoolManager->setCurrentSchool($userPayload['school']);
        return $this->userManager->findUserById($userPayload['id']);
    }

    /**
     * Always return true.
     * @param mixed         $credentials
     * @param UserInterface $user
     * @return true
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Don't do anything, just keep going.
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        throw new AccessDeniedHttpException($exception->getMessage(), null, $exception->getCode());
    }

    /**
     * Called when authentication is needed, but no token is sent.
     * @param Request                 $request
     * @param AuthenticationException $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    public function supportsRememberMe()
    {
        return false;
    }
}