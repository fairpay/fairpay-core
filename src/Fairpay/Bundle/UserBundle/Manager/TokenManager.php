<?php


namespace Fairpay\Bundle\UserBundle\Manager;


use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Repository\TokenRepository;
use Fairpay\Util\Manager\EntityManager;
use Fairpay\Util\Util\TokenGeneratorInterface;

/**
 * @method TokenRepository getRepo()
 */
class TokenManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayUserBundle:Token';

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /**
     * TokenManager constructor.
     * @param TokenGeneratorInterface $tokenGenerator
     */
    public function __construct(TokenGeneratorInterface $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Create a unique random Token for a User.
     *
     * @param User       $user
     * @param integer    $type
     * @param array|null $payload
     * @param bool       $removePrevious
     * @return Token
     */
    public function create(User $user, $type, array $payload = null, $removePrevious = true)
    {
        if ($removePrevious) {
            $this->getRepo()->removeToken($user, $type);
        }

        $token = new Token($user, $type, $this->tokenGenerator->generateToken(), $payload);

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    /**
     * Find a Token object from it's string token.
     *
     * @param $token
     * @return null|Token
     */
    public function find($token)
    {
        return $this->getRepo()->findOneBy(['token' => $token]);
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}