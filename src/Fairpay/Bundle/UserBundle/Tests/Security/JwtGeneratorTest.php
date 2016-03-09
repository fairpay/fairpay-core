<?php


namespace Fairpay\Bundle\UserBundle\Tests\Security;


use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Encryption\Factory;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Token;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Security\JwtGenerator;
use Fairpay\Util\Tests\WebTestCase;

class JwtGeneratorTest extends WebTestCase
{
    /** @var  JwtGenerator */
    public $jwtGenerator;

    /** @var  School */
    public $school;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->jwtGenerator = $this->container->get('jwt_generator');
        $this->school = null;
    }

    public function testGetToken()
    {
        $this->havingASchool();
        $user = $this->getBatman();

        $token = $this->jwtGenerator->generate($user);
        $payload = json_decode(base64_decode(explode('.', $token)[1]), true);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('iss', $payload);
        $this->assertArrayHasKey('user', $payload);

        $this->assertEquals($user->getId(), $payload['user']['id']);
        $this->assertEquals($user->getDisplayName(), $payload['user']['name']);
        $this->assertEquals($this->school->getSlug(), $payload['user']['school']);
    }

    public function testIsValid()
    {
        $this->havingASchool();
        $user = $this->getBatman();

        $token = $this->generateToken($user, $this->school);
        $this->assertTrue($this->jwtGenerator->isValid($this->jwtGenerator->decode($token)));

        $token = $this->generateToken($user, $this->school, '-1 second');
        $this->assertStringStartsWith('Token expired at', $this->jwtGenerator->isValid($this->jwtGenerator->decode($token)));

        $token = $this->generateToken($user, $this->school, '30 minutes', 'fake.issuer');
        $this->assertEquals('Issuer is invalid.', $this->jwtGenerator->isValid($this->jwtGenerator->decode($token)));

        $token = $this->generateToken($user, $this->school, '30 minutes', 'localhost', 'fakeSecretKey');
        $this->assertEquals('Signature is invalid.', $this->jwtGenerator->isValid($this->jwtGenerator->decode($token)));
    }

    /**
     * Create a School in the DB and set it as current School.
     * @param bool $currentSchool
     */
    protected function havingASchool($currentSchool = true)
    {
        $school = new School(uniqid(), uniqid());
        $school->setSlug(uniqid());

        $this->em->persist($school);
        $this->em->flush();

        $this->school = $school;

        if ($currentSchool) {
            $this->container->get('school_manager')->setCurrentSchool($this->school);
        }
    }

    protected function getBatman()
    {
        $user = new User();
        $user->setDisplayName('Batman');
        $user->setId(42);

        return $user;
    }

    protected function generateToken(User $user, School $school, $exp = '30 minutes', $iss = 'localhost', $secret = null)
    {
        $jwt = new Jwt();
        $hs256 = new Hs256($secret === null ? $this->container->getParameter('secret') : $secret);

        $token = new Token();
        $token->addClaim(new Claim\Expiration(new \DateTime($exp)));
        $token->addClaim(new Claim\IssuedAt(new \DateTime('now')));
        $token->addClaim(new Claim\Issuer($iss));
        $token->addClaim(new Claim\PublicClaim('user', array(
            'id' => $user->getId(),
            'name' => $user->getDisplayName(),
            'school' => $school->getSlug(),
        )));

        return $jwt->serialize($token, Factory::create($hs256));
    }
}