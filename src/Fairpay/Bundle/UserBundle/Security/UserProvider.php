<?php


namespace Fairpay\Bundle\UserBundle\Security;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /** @var  UserManager */
    protected $userManager;

    /**
     * UserProvider constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Loads the user for the given username or email.
     *
     * @param string $username The username
     * @return UserInterface
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!$user) {
            if ($this->userManager->isEmail($username)) {
                throw new UsernameNotFoundException(sprintf('Email "%s" does not exist.', $username));
            } else {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }
        }

        return $user;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * @param UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Fairpay\Bundle\UserBundle\Entity\User, but got "%s".', get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserById($user->getId())) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $userClass = 'Fairpay\Bundle\UserBundle\Entity\User';
        return $userClass === $class || is_subclass_of($class, $userClass);
    }
}