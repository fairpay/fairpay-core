<?php


namespace Fairpay\Bundle\UserBundle\Request\ParamConverter;


use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Manager\TokenManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TokenParamConverter implements ParamConverterInterface
{
    /** @var  TokenManager */
    private $tokenManager;

    /** @var  SchoolManager */
    private $schoolManager;

    /**
     * TokenParamConverter constructor.
     * @param TokenManager  $tokenManager
     * @param SchoolManager $schoolManager
     */
    public function __construct(TokenManager $tokenManager, SchoolManager $schoolManager)
    {
        $this->tokenManager  = $tokenManager;
        $this->schoolManager = $schoolManager;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $token = $this->tokenManager->find($request->attributes->get('token'));

        if (null === $token) {
            throw new NotFoundHttpException('Ce token n\'existe pas.');
        }

        if ($token->getUser()->getSchool() != $this->schoolManager->getCurrentSchool()) {
            throw new NotFoundHttpException('Ce token n\'existe pas.');
        }

        $request->attributes->set($configuration->getName(), $token);
        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === Token::class;
    }
}