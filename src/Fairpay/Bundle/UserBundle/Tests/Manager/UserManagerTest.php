<?php


namespace Fairpay\Bundle\UserBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Util\Tests\UnitTestCase;
use Fairpay\Util\Util\StringUtil;
use Fairpay\Util\Util\TokenGenerator;
use Prophecy\Argument;

class UserManagerTest extends UnitTestCase
{
    const user_repository       = 'Fairpay\Bundle\UserBundle\Repository\UserRepository';
    const user_password_encoder = 'Symfony\Component\Security\Core\Encoder\UserPasswordEncoder';

    /** @var  UserManager */
    public $userManager;

    // Mocked
    private $em;
    private $repo;
    private $dispatcher;
    private $passwordEncoder;
    private $schoolManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->passwordEncoder = $this->mock(self::user_password_encoder);

        $this->userManager = new UserManager(
            $this->passwordEncoder->reveal(),
            new TokenGenerator(),
            new StringUtil()
        );

        $this->em = $this->mock(self::doctrine_orm_entity_manager);
        $this->dispatcher = $this->mock(self::event_dispatcher);
        $this->userManager->init($this->em->reveal(), $this->dispatcher->reveal());

        $this->schoolManager = $this->mock(self::school_manager);
        $this->schoolManager->getCurrentSchool()->willReturn(null);
        $this->userManager->setSchoolManager($this->schoolManager->reveal());

        $this->repo = $this->mock(self::user_repository);
        $this->em->getRepository(UserManager::ENTITY_SHORTCUT_NAME)->willReturn($this->repo->reveal());
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
        $this->userManager->createMainVendor('Bruce Wayne', 'b4atman', 'bruce@wayne');
    }

    public function testCreateUser()
    {
        $this->havingASchool();
        $this->havingTakenUsernames();

        $this->em->persist(Argument::type(User::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
        $this->passwordEncoder->encodePassword(Argument::type(User::class), 'b4atman')->willReturn('encoded_password');

        $user = $this->userManager->createMainVendor('Bruce Wayne', 'b4atman', 'bruce@wayne');

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
     * Current School is set.
     */
    protected function havingASchool()
    {
        $this->schoolManager->getCurrentSchool()->willReturn(new School());
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
        $this->repo->findTakenUsernames(Argument::type(School::class), Argument::any())->willReturn($usernames);
    }
}