<?php


namespace Fairpay\Bundle\UserBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Util\Manager\NoCurrentSchoolException;
use Fairpay\Util\Tests\WebTestCase;

class UserManagerTest extends WebTestCase
{
    /** @var  UserManager */
    public $userManager;

    /** @var  School */
    public $school;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->userManager = $this->container->get('user_manager');
        $this->school = null;
    }

    public function isEmailProvider()
    {
        return [[array(
            ['username', false],
            ['email@domain', true],
        )]];
    }

    /**
     * @dataProvider isEmailProvider
     * @param $data
     */
    public function testIsEmail($data)
    {
        foreach ($data as list($email, $expected)) {
            $this->assertEquals($expected, $this->userManager->isEmail($email));
        }
    }

    public function testFindUserById()
    {
        $this->havingASchool();
        $user = $this->havingAUser();

        try {
            $this->userManager->findUserById($user->getId());
            $this->fail('Should not be able to find a user without having a current School defined.');
        } catch (NoCurrentSchoolException $e) {
            // Should throw an exception when no current School is defined.
        }

        // Should work when current school is defined
        $this->container->get('school_manager')->setCurrentSchool($this->school);
        $this->assertEquals($user->getId(), $this->userManager->findUserById($user->getId())->getId());

        // Should not find users from other schools
        $this->havingASchool();
        $user = $this->havingAUser();
        $this->assertNull($this->userManager->findUserById($user->getId()));
    }

    public function testFindUserByUserNameOrEmail()
    {
        $this->havingASchool();
        $user = $this->havingAUser('username', 'email@domain');

        try {
            $this->userManager->findUserByUsernameOrEmail($user->getUsername());
            $this->fail('Should not be able to find a user without having a current School defined.');
        } catch (NoCurrentSchoolException $e) {
            // Should throw an exception when no current School is defined.
        }

        // Should work when current school is defined
        $this->container->get('school_manager')->setCurrentSchool($this->school);
        $this->assertEquals($user->getId(), $this->userManager->findUserByUsernameOrEmail($user->getUsername())->getId());
        $this->assertEquals($user->getId(), $this->userManager->findUserByUsernameOrEmail($user->getEmail())->getId());

        // Should not find users from other schools
        $this->havingASchool();
        $user = $this->havingAUser('other_name', 'other@email');
        $this->assertNull($this->userManager->findUserByUsernameOrEmail($user->getUsername()));
        $this->assertNull($this->userManager->findUserByUsernameOrEmail($user->getEmail()));
    }

    public function testCreateUser()
    {
        $this->havingASchool();

        try {
            $this->userManager->createMainVendor('Bruce Wayne', 'b4atman', 'bruce@wayne');
            $this->fail('Should not be able to find a user without having a current School defined.');
        } catch (NoCurrentSchoolException $e) {
            // Should throw an exception when no current School is defined.
        }

        // Should work when current school is defined
        $this->container->get('school_manager')->setCurrentSchool($this->school);
        $user = $this->userManager->createMainVendor('Bruce Wayne', 'b4atman', 'bruce@wayne');

        $this->assertNotNull($user->getId());
        $this->assertEquals('bruce.wayne', $user->getUsername());
        $this->assertEquals('Bruce Wayne', $user->getDisplayName());
        $this->assertEquals('bruce@wayne', $user->getEmail());
        $this->assertNotNull($user->getSalt());
        $this->assertNotNull($user->getPassword());
        $this->assertNotEquals('b4atman', $user->getPassword());
    }

    public function testUsernameFromDisplayName()
    {
        $this->havingASchool();
        $this->havingAUser('username');
        $this->havingAUser('username_again');

        try {
            $this->userManager->usernameFromDisplayName('Bruce Wayne');
            $this->fail('Should not be able to find a user without having a current School defined.');
        } catch (NoCurrentSchoolException $e) {
            // Should throw an exception when no current School is defined.
        }

        $this->container->get('school_manager')->setCurrentSchool($this->school);

        $this->assertEquals('only.username', $this->userManager->usernameFromDisplayName('Only Username'));
        $this->assertEquals('username1', $this->userManager->usernameFromDisplayName('Username!'));

        $this->havingAUser('username1');
        $this->havingAUser('username2');
        $this->havingAUser('username4');

        $this->assertEquals('username3', $this->userManager->usernameFromDisplayName('username'));
    }

    /**
     * Create a School in the DB.
     * @param $name
     * @param $email
     * @param $slug
     */
    protected function havingASchool($name = null, $email = null, $slug = null)
    {
        $school = new School($name ? $name : uniqid(), $email ? $email : uniqid());
        $school->setSlug($slug ? $slug : uniqid());

        $this->em->persist($school);
        $this->em->flush();

        $this->school = $school;
    }

    /**
     * Create a School in the DB.
     * @param $username
     * @return User
     */
    public function havingAUser($username = null, $email = null)
    {
        $user = new User();
        $user->setDisplayName(uniqid());
        $user->setSalt(uniqid());
        $user->setPassword(uniqid());
        $user->setEmail($email ? $email : uniqid());
        $user->setSchool($this->school);
        $user->setUsername($username ? $username : uniqid());

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}