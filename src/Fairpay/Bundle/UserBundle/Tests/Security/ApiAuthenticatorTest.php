<?php


namespace Fairpay\Bundle\UserBundle\Tests\Security;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Util\Tests\WebTestCase;

class ApiAuthenticatorTest extends WebTestCase
{
    /** @var  School */
    private $school;

    /** @var  User */
    private $user;

    public function testAuth()
    {
        $this->api->get('/students');
        $this->assertEquals(401, $this->api->response->status);

        $this->api->setToken('TOKEN');
        $this->api->get('/students');
        $this->assertEquals(403, $this->api->status);

        $this->havingASchool();
        $this->havingAUser();
        $this->api->setToken($this->container->get('jwt_generator')->generate($this->user, '-30 minutes'));
        $this->api->get('/students');
        $this->assertEquals(403, $this->api->status);

        $this->api->setToken($this->container->get('jwt_generator')->generate($this->user, '30 minutes'));
        $this->api->get('/students');
        $this->assertEquals(200, $this->api->status);
    }

    protected function havingASchool()
    {
        $school = new School('ESIEE Paris', 'bde@edu.esiee.fr');
        $school->setSlug('esiee');

        $this->em->persist($school);
        $this->em->flush();

        $this->school = $school;

        $this->container->get('school_manager')->setCurrentSchool($school);
    }

    protected function havingAUser()
    {
        $user = new User();
        $user->setUsername('batman');
        $user->setDisplayName('Bruce Wayne');
        $user->setEmail('bruce@wayne.com');
        $user->setPassword('xxx');
        $user->setSalt('pepper');
        $user->setSchool($this->school);

        $this->em->persist($user);
        $this->em->flush();

        $this->user = $user;
    }
}