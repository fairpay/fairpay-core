<?php


namespace Fairpay\Bundle\UserBundle\Manager;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Repository\UserRepository;
use Fairpay\Util\Manager\EntityManager;

/**
 * @property UserRepository $repo
 */
class UserManager extends EntityManager
{
    const ENTITY_SHORTCUT_NAME = 'FairpayUserBundle:User';

    /**
     * @param string $username
     * @return User|null
     */
    public function findUserByUsernameOrEmail($username)
    {
        if ($this->isEmail($username)) {
            return $this->repo->findByEmail($username);
        } else {
            return $this->repo->findByUsername($username);
        }
    }

    /**
     * @param $id
     * @return User|null
     */
    public function findUserById($id)
    {
        return $this->repo->findUserById($id);
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