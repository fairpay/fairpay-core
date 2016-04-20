<?php


namespace Fairpay\Bundle\UserBundle\Request\ParamConverter;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserParamConverter implements ParamConverterInterface
{
    /** @var  UserManager */
    private $userManager;

    /**
     * TokenParamConverter constructor.
     * @param UserManager   $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager  = $userManager;
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
        $user = $this->userManager->findUserById($request->attributes->get('id'));

        if ('vendor' === $configuration->getName()) {
            if (null === $user || !$user->getIsVendor()) {
                throw new NotFoundHttpException('Ce marchand n\'existe pas.');
            }
        } else {
            if (null === $user) {
                throw new NotFoundHttpException('Cet utilisateur n\'existe pas.');
            }
        }

        $request->attributes->set($configuration->getName(), $user);
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
        return $configuration->getClass() === User::class;
    }
}