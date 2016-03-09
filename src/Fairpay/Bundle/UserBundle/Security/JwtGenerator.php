<?php


namespace Fairpay\Bundle\UserBundle\Security;

use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Encryption\Factory;
use Emarref\Jwt\Exception\VerificationException;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Token;
use Emarref\Jwt\Verification\Context;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Manager\NoCurrentSchoolException;

class JwtGenerator
{
    private $baseHost;
    private $secret;

    /** @var  Jwt */
    private $jwt;

    /** @var  Hs256 */
    private $hs256;

    /** @var  SchoolManager */
    private $schoolManager;

    /**
     * JwtGenerator constructor.
     * @param               $baseHost
     * @param               $secret
     * @param SchoolManager $schoolManager
     */
    public function __construct($baseHost, $secret, SchoolManager $schoolManager)
    {
        $this->baseHost = $baseHost;
        $this->secret = $secret;
        $this->schoolManager = $schoolManager;
        $this->jwt = new Jwt();
        $this->hs256 = new Hs256($this->secret);
    }

    /**
     * Generate a 30 minutes jwt.
     * @param User   $user
     * @param string $exp
     * @return string
     * @throws NoCurrentSchoolException
     */
    public function generate(User $user, $exp = '30 minutes')
    {
        $school = $this->schoolManager->getCurrentSchool();
        if (null === $school) {
            throw new NoCurrentSchoolException();
        }

        $token = new Token();
        $token->addClaim(new Claim\Expiration(new \DateTime($exp)));
        $token->addClaim(new Claim\IssuedAt(new \DateTime('now')));
        $token->addClaim(new Claim\Issuer($this->baseHost));
        $token->addClaim(new Claim\PublicClaim('user', array(
            'id' => $user->getId(),
            'name' => $user->getDisplayName(),
            'school' => $school->getSlug(),
        )));

        return $this->jwt->serialize($token, Factory::create($this->hs256));
    }

    /**
     * Decode a jwt.
     * @param string $token
     * @return Token|null
     */
    public function decode($token)
    {
        try {
            return $this->jwt->deserialize($token);
        } catch(\RuntimeException $e) {
            return null;
        }
    }

    /**
     * Make sure the JWT is valid.
     * @param Token $token
     * @return bool|string
     */
    public function isValid(Token $token = null)
    {
        if (null === $token) {
            return 'Le jeton d\'authentification n\'est pas valide.';
        }

        $context = new Context(Factory::create($this->hs256));
        $context->setIssuer($this->baseHost);

        try {
            $this->jwt->verify($token, $context);
        } catch (VerificationException $e) {
            return $e->getMessage();
        }

        return true;
    }
}