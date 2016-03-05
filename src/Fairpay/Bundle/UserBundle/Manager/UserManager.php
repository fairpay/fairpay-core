<?php


namespace Fairpay\Bundle\UserBundle\Manager;


use Doctrine\ORM\EntityManager as DoctrineEM;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Repository\UserRepository;
use Fairpay\Util\Manager\EntityManager;
use Fairpay\Util\Util\StringUtil;
use Fairpay\Util\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * @property UserRepository $repo
 */
class UserManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayUserBundle:User';

    /** @var  SchoolManager */
    protected $schoolManager;

    /** @var  UserPasswordEncoder */
    protected $passwordEncoder;

    /** @var TokenGeneratorInterface */
    private $tokenGenerator;

    /** @var  StringUtil */
    private $stringUtil;

    public function __construct(
        DoctrineEM $em,
        TraceableEventDispatcher $dispatcher,
        SchoolManager $schoolManager,
        UserPasswordEncoder $passwordEncoder,
        TokenGeneratorInterface $tokenGenerator,
        StringUtil $stringUtil
    ) {
        parent::__construct($em, $dispatcher);
        $this->schoolManager = $schoolManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->stringUtil = $stringUtil;
    }

    /**
     * Create a user and save it to DB.
     *
     * @param string $displayName
     * @param string $plainPassword
     * @param string $email
     * @return User
     * @throws NoCurrentSchoolException
     */
    public function create($displayName, $plainPassword, $email)
    {
        $user = new User();
        $user->setDisplayName($displayName);
        $user->setSalt($this->tokenGenerator->generateToken());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setEmail($email);
        $user->setSchool($this->getCurrentSchool());
        $user->setUsername($this->usernameFromDisplayName($displayName));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param string $displayName
     * @return string
     * @throws NoCurrentSchoolException
     */
    public function usernameFromDisplayName($displayName)
    {
        $username = $this->stringUtil->urlize($displayName, '.');
        $takenUsernames = $this->repo->findTakenUsernames($this->getCurrentSchool(), $username);
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
            return $this->repo->findByEmail($this->getCurrentSchool(), $username);
        } else {
            return $this->repo->findByUsername($this->getCurrentSchool(), $username);
        }
    }

    /**
     * @param $id
     * @return User|null
     * @throws NoCurrentSchoolException
     */
    public function findUserById($id)
    {
        return $this->repo->findUserById($this->getCurrentSchool(), $id);
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
     * @return School
     * @throws NoCurrentSchoolException
     */
    protected function getCurrentSchool()
    {
        $school = $this->schoolManager->getCurrentSchool();

        if (null === $school) {
            throw new NoCurrentSchoolException('Impossible to perform action, no current School is defined.');
        }

        return $school;
    }

    public function getEntityShortcutName()
    {
        return self::ENTITY_SHORTCUT_NAME;
    }
}