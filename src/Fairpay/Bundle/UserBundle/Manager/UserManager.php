<?php


namespace Fairpay\Bundle\UserBundle\Manager;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Form\UserSetPassword;
use Fairpay\Bundle\UserBundle\Repository\UserRepository;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;
use Fairpay\Util\Manager\NoCurrentSchoolException;
use Fairpay\Util\Util\StringUtil;
use Fairpay\Util\Util\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * @method UserRepository getRepo()
 */
class UserManager extends CurrentSchoolAwareManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayUserBundle:User';

    /** @var  UserPasswordEncoder */
    protected $passwordEncoder;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /** @var  StringUtil */
    private $stringUtil;

    /** @var  TokenStorage */
    private $tokenStorage;

    public function __construct(
        UserPasswordEncoder $passwordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        StringUtil $stringUtil,
        TokenStorage $tokenStorage
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->stringUtil = $stringUtil;
        $this->tokenStorage = $tokenStorage;
    }

    public function login(User $user)
    {
        $token = new UsernamePasswordToken($user, null, 'fairpay_db', $user->getRoles());
        $this->tokenStorage->setToken($token);
    }

    /**
     * Create the main vendor and save it to DB.
     *
     * @param string $displayName
     * @param string $plainPassword
     * @param string $email
     * @return User
     * @throws NoCurrentSchoolException
     */
    public function createMainVendor($displayName, $plainPassword, $email)
    {
        $user = $this->newUser($displayName);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setEmail($email);
        $user->setIsVendor(true);

        $this->em->persist($user);
        $this->em->flush();

        $this->dispatcher->dispatch(
            UserCreatedEvent::onUserCreated,
            new UserCreatedEvent($user, UserCreatedEvent::REGISTERED_WITH_SCHOOL)
        );

        return $user;
    }

    /**
     * Create a user based on a student and link it.
     *
     * @param Student $student
     * @return User
     */
    public function createFromStudent(Student $student)
    {
        $user = $this->newUser((string) $student);
        $user->setPassword($this->tokenGenerator->generateToken());
        $user->setEmail($student->getEmail());
        $user->setIsVendor(false);

        $user->setStudent($student);
        $student->setUser($user);

        $this->em->persist($user);
        $this->em->persist($student);
        $this->em->flush();

        $this->dispatcher->dispatch(
            UserCreatedEvent::onUserCreated,
            new UserCreatedEvent($user, UserCreatedEvent::REGISTERED_BY_ADMIN)
        );

        return $user;
    }

    /**
     * Create a new User object.
     *
     * @param $displayName
     * @return User
     * @throws NoCurrentSchoolException
     */
    private function newUser($displayName)
    {
        $user = new User();
        $user->setDisplayName($displayName);
        $user->setSalt($this->tokenGenerator->generateToken());
        $user->setSchool($this->getCurrentSchool());
        $user->setUsername($this->usernameFromDisplayName($displayName));

        return $user;
    }

    /**
     * Set $user's password and save it to DB.
     *
     * @param User            $user
     * @param UserSetPassword $setPassword
     */
    public function setPassword(User $user, UserSetPassword $setPassword)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $setPassword->plainPassword));

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param string $displayName
     * @return string
     * @throws NoCurrentSchoolException
     */
    public function usernameFromDisplayName($displayName)
    {
        $username = $this->stringUtil->urlize($displayName, '.');
        $takenUsernames = $this->getRepo()->findTakenUsernames($this->getCurrentSchool(), $username);
        $suffix = '';

        while (in_array($username . $suffix, $takenUsernames)) {
            $suffix++;
        }

        return $username . $suffix;
    }

    /**
     * @param string $username
     * @return User|null
     * @throws NoCurrentSchoolException
     */
    public function findUserByUsernameOrEmail($username)
    {
        if ($this->isEmail($username)) {
            return $this->getRepo()->findByEmail($this->getCurrentSchool(), $username);
        } else {
            return $this->getRepo()->findByUsername($this->getCurrentSchool(), $username);
        }
    }

    /**
     * @param $id
     * @return User|null
     * @throws NoCurrentSchoolException
     */
    public function findUserById($id)
    {
        return $this->getRepo()->findUserById($this->getCurrentSchool(), $id);
    }

    /**
     * Check if string looks like an email.
     * @param string $email
     * @return bool
     */
    public function isEmail($email)
    {
        return false !== strpos($email, '@');
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}