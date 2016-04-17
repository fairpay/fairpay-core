<?php


namespace Fairpay\Bundle\UserBundle\Manager;

use Fairpay\Bundle\StudentBundle\Entity\Student;
use Fairpay\Bundle\StudentBundle\Manager\StudentManager;
use Fairpay\Bundle\UserBundle\Entity\Token;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Event\UserEvent;
use Fairpay\Bundle\UserBundle\Event\UserRequestResetPassword;
use Fairpay\Bundle\UserBundle\Exception\NotAllowedEmailDomainException;
use Fairpay\Bundle\UserBundle\Exception\UnregisteredEmailsNotAllowedException;
use Fairpay\Bundle\UserBundle\Form\AbstractUserSetPassword;
use Fairpay\Bundle\UserBundle\Repository\UserRepository;
use Fairpay\Util\Email\Services\EmailHelper;
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

    /** @var  TokenManager */
    private $tokenManager;

    /** @var  StudentManager */
    private $studentManager;

    /** @var  EmailHelper */
    private $emailHelper;

    public function __construct(
        UserPasswordEncoder $passwordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        StringUtil $stringUtil,
        TokenStorage $tokenStorage,
        TokenManager $tokenManager,
        StudentManager $studentManager,
        EmailHelper $emailHelper
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator  = $tokenGenerator;
        $this->stringUtil      = $stringUtil;
        $this->tokenStorage    = $tokenStorage;
        $this->tokenManager    = $tokenManager;
        $this->studentManager  = $studentManager;
        $this->emailHelper     = $emailHelper;
    }

    /**
     * Login a user.
     *
     * @param User $user
     */
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

        $token = $this->tokenManager->create($user, Token::REGISTER);

        $this->dispatcher->dispatch(
            UserCreatedEvent::onUserCreated,
            new UserCreatedEvent($user, UserCreatedEvent::REGISTERED_WITH_SCHOOL, $token)
        );

        return $user;
    }

    /**
     * Create a user based on a student.
     *
     * @param Student $student
     * @param int     $trigger
     * @return User
     */
    public function createFromStudent(Student $student, $trigger = UserCreatedEvent::REGISTERED_BY_ADMIN)
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

        $token = $this->tokenManager->create($user, Token::REGISTER);

        $this->dispatcher->dispatch(
            UserCreatedEvent::onUserCreated,
            new UserCreatedEvent($user, $trigger, $token)
        );

        return $user;
    }

    /**
     * Create a User based on an email.
     *
     * @param $email
     * @return User
     * @throws NotAllowedEmailDomainException
     * @throws UnregisteredEmailsNotAllowedException
     */
    public function createFromEmail($email)
    {
        $student = $this->studentManager->findStudentByEmail($email);

        if (!$student) {
            $school = $this->schoolManager->getCurrentSchool();
            $domain = $this->emailHelper->getDomain($email);

            if (!$school->getAllowUnregisteredEmails()) {
                throw new UnregisteredEmailsNotAllowedException();
            }

            if (!in_array($domain, $school->getAllowedEmailDomains())) {
                throw new NotAllowedEmailDomainException();
            }

            $student = $this->studentManager->createBlank($email);
        }

        return $this->createFromStudent($student, UserCreatedEvent::SELF_REGISTERED);
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
     * @param User                    $user
     * @param AbstractUserSetPassword $setPassword
     */
    public function setPassword(User $user, AbstractUserSetPassword $setPassword)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $setPassword->plainPassword));

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Send the user a link to reset his password or to finish registration.
     * If a User with this particular email does not exist, do nothing.
     * @param User|string $user a User object, email, or username
     * @return bool true if a User is found
     */
    public function requestResetPassword($user)
    {
        if (!$user instanceof User) {
            $user = $this->findUserByUsernameOrEmail($user);
        }

        if ($user) {
            $token = $this->tokenManager->getToken($user, Token::REGISTER);

            if (!$token) {
                $token = $this->tokenManager->create($user, Token::RESET_PASSWORD);
            }

            $this->dispatcher->dispatch(
                UserEvent::onUserRequestResetPassword,
                new UserRequestResetPassword($user, $token)
            );

            return true;
        }

        return false;
    }

    /**
     * If a User with this particular email exist send him a link to finish registration.
     *
     * @param User|string $user a User object, email, or username
     * @return bool true if a User is found
     */
    public function requestResendRegistrationEmail($user)
    {
        if (!$user instanceof User) {
            $user = $this->findUserByUsernameOrEmail($user);
        }

        if ($user) {
            $token = $this->tokenManager->getToken($user, Token::REGISTER);

            if (!$token) {
                $token = $this->tokenManager->create($user, Token::REGISTER);
            }

            $this->dispatcher->dispatch(
                UserEvent::onUserCreated,
                new UserCreatedEvent($user, UserCreatedEvent::SELF_REGISTERED, $token)
            );

            return true;
        }

        return false;
    }

    /**
     * @param string $displayName
     * @param User   $user
     * @return string
     * @throws NoCurrentSchoolException
     */
    public function usernameFromDisplayName($displayName, User $user = null)
    {
        $username = $this->stringUtil->urlize($displayName, '.');
        $takenUsernames = $this->getRepo()->findTakenUsernames($this->getCurrentSchool(), $username, $user);
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

    /**
     * Get current active User.
     * @return User|null
     */
    public function getActiveUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}