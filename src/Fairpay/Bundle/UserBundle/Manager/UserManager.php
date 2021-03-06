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
use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder;
use Fairpay\Bundle\VendorBundle\Entity\Group;
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
     * Create a vendor and save it to DB.
     * If $plainPassword is null, then an email will be sent to invite the vendor to finish the registration.
     *
     * @param string      $displayName
     * @param string      $email
     * @param null|string $plainPassword
     * @param int         $trigger
     * @return User
     * @throws NoCurrentSchoolException
     */
    public function createVendor($displayName, $email, $plainPassword = null, $trigger = UserCreatedEvent::REGISTERED_BY_ADMIN)
    {
        $user = $this->newUser($displayName);
        $user->setEmail($email);
        $user->setIsVendor(true);

        if ($plainPassword) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        } else {
            $user->setPassword($this->tokenGenerator->generateToken());
        }

        $this->em->persist($user);
        $this->em->flush();

        $token = $plainPassword ? null : $this->tokenManager->create($user, Token::REGISTER);

        $this->dispatcher->dispatch(
            UserCreatedEvent::onUserCreated,
            new UserCreatedEvent($user, $trigger, $token)
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
     * Remove the permission of a $user regarding a particular $vendor.
     * @param User $user
     * @param User $vendor
     */
    public function removePermission(User $user, User $vendor)
    {
        $permissions = $user->getPermissions();

        unset($permissions[$vendor->getId()]);
        $this->dispatchGlobalPermissions($permissions);

        $user->setPermissions($permissions);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Remove the permission of a $user regarding a particular $vendor.
     * @param User  $user
     * @param Group $group
     */
    public function setPermission(User $user, Group $group)
    {
        $permissions = $user->getPermissions();

        $permissions[$group->getVendor()->getId()] = $group->getMask();
        $this->dispatchGlobalPermissions($permissions);

        $user->setPermissions($permissions);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * Recompute the global roles from each vendor specific roles.
     * @param $permissions
     */
    private function dispatchGlobalPermissions(&$permissions)
    {
        $builder = new MaskBuilder();

        foreach ($permissions as $id => $mask) {
            if ($id === 'global') {
                continue;
            }

            $builder->addAllGlobalRoles($mask);
        }

        $permissions['global'] = $builder->get();
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