<?php


namespace Fairpay\Bundle\UserBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\StudentBundle\Manager\StudentManager;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\TokenManager;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Util\Email\Services\EmailHelper;
use Fairpay\Util\Tests\UnitTestCase;
use Fairpay\Util\Util\StringUtil;
use Fairpay\Util\Util\TokenGenerator;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserManagerTest extends UnitTestCase
{
    /** @var  UserManager */
    public $userManager;

    // Mocked
    protected $passwordEncoder;
    protected $tokenManager;
    protected $studentManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->passwordEncoder = $this->mock(UserPasswordEncoder::class);
        $this->tokenManager = $this->mock(TokenManager::class);
        $this->tokenManager->create(Argument::type(User::class), Argument::any())->will(function($args) {
            return new Token($args[0], $args[1], 'token');
        });
        $this->studentManager = $this->mock(StudentManager::class);

        $this->userManager = new UserManager(
            $this->passwordEncoder->reveal(),
            new TokenGenerator(),
            new StringUtil(),
            new TokenStorage(),
            $this->tokenManager->reveal(),
            $this->studentManager->reveal(),
            new EmailHelper([], [])
        );

        $this->initManager($this->userManager);
    }

    public function isEmailProvider()
    {
        return [
            ['username', false],
            ['email@domain', true],
        ];
    }

    /**
     * @dataProvider isEmailProvider
     * @param $email
     * @param $expected
     */
    public function testIsEmail($email, $expected)
    {
        $this->assertEquals($expected, $this->userManager->isEmail($email));
    }

    /**
     * @expectedException Fairpay\Util\Manager\NoCurrentSchoolException
     */
    public function testFindUserByIdWithoutSchool()
    {
        $this->userManager->findUserById(42);
    }

    public function testFindUserById()
    {
        $this->havingAUser();
        $this->havingASchool();

        $user = $this->userManager->findUserById(42);

        $this->assertNotNull($user);
        $this->schoolManager->getCurrentSchool()->shouldHaveBeenCalled();
    }

    /**
     * @expectedException Fairpay\Util\Manager\NoCurrentSchoolException
     */
    public function testFindUserByUserNameOrEmailWithoutSchool()
    {
        $this->userManager->findUserByUsernameOrEmail('username');
    }

    public function testFindUserByUserNameOrEmail()
    {
        $this->havingAUser();
        $this->havingASchool();

        $this->userManager->findUserByUsernameOrEmail('username');
        $this->repo->findByUsername(Argument::type(School::class), 'username')->shouldHaveBeenCalled();

        $this->userManager->findUserByUsernameOrEmail('email@domain');
        $this->repo->findByEmail(Argument::type(School::class), 'email@domain')->shouldHaveBeenCalled();
    }

    /**
     * @expectedException Fairpay\Util\Manager\NoCurrentSchoolException
     */
    public function testCreateUserWithoutSchool()
    {
        $this->userManager->createVendor('Bruce Wayne', 'bruce@wayne', 'b4atman');
    }

    public function testCreateUser()
    {
        $this->havingASchool();
        $this->havingTakenUsernames();

        $this->shouldBePersisted(User::class);
        $this->passwordEncoder
            ->encodePassword(Argument::type(User::class), 'b4atman')
            ->willReturn('encoded_password');

        $user = $this->userManager->createVendor('Bruce Wayne', 'bruce@wayne', 'b4atman');

        $this->assertEquals('bruce.wayne', $user->getUsername());
        $this->assertEquals('Bruce Wayne', $user->getDisplayName());
        $this->assertEquals('bruce@wayne', $user->getEmail());
        $this->assertEquals('encoded_password', $user->getPassword());
        $this->assertEquals(true, $user->getIsVendor());
        $this->assertNotNull($user->getSalt());
    }

    /**
     * @expectedException Fairpay\Util\Manager\NoCurrentSchoolException
     */
    public function testUsernameFromDisplayNameWithoutSchool()
    {
        $this->userManager->usernameFromDisplayName('Bruce Wayne');
    }

    public function usernameFromDisplayNameProvider()
    {
        return [
            [[], 'Only Username', 'only.username'],
            [['username'], 'Username!', 'username1'],
            [['username', 'username1', 'username2', 'username4'], 'username', 'username3'],
        ];
    }

    /**
     * @dataProvider usernameFromDisplayNameProvider
     * @param $takenUsernames
     * @param $displayName
     * @param $expected
     */
    public function testUsernameFromDisplayName($takenUsernames, $displayName, $expected)
    {
        $this->havingASchool();
        $this->havingTakenUsernames($takenUsernames);

        $username = $this->userManager->usernameFromDisplayName($displayName);

        $this->assertEquals($expected, $username);
    }

    /**
     * findUserById, findByEmail, and findByUsername wil return a User.
     */
    public function havingAUser()
    {
        $this->repo->findUserById(Argument::type(School::class), Argument::any())->willReturn(new User());
        $this->repo->findByEmail(Argument::type(School::class), Argument::any())->willReturn(new User());
        $this->repo->findByUsername(Argument::type(School::class), Argument::any())->willReturn(new User());
    }

    /**
     * findTakenUsernames will return the $usernames.
     * @param array $usernames
     */
    public function havingTakenUsernames(array $usernames = [])
    {
        $this->repo->findTakenUsernames(Argument::type(School::class), Argument::any(), null)->willReturn($usernames);
    }
}