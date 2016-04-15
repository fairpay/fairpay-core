<?php

namespace Fairpay\Bundle\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\UserBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * @param School $school
     * @param string $username
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByUsername(School $school, $username)
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.username = :username')
                ->andWhere('u.school = :school')
                ->setParameter('username', $username)
                ->setParameter('school', $school)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param School $school
     * @param string $email
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findByEmail(School $school, $email)
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.email = :email')
                ->andWhere('u.school = :school')
                ->setParameter('email', $email)
                ->setParameter('school', $school)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @param School $school
     * @param        $id
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findUserById(School $school, $id)
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.id = :id')
                ->andWhere('u.school = :school')
                ->setParameter('id', $id)
                ->setParameter('school', $school)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Get all usernames from $school that starts with $username.
     * @param School $school
     * @param string $username
     * @param User   $user
     * @return array
     */
    public function findTakenUsernames(School $school, $username, User $user = null)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('u.username')
            ->where('u.username LIKE :username')
            ->andWhere('u.school = :school')
            ->setParameter('username', $username . '%')
            ->setParameter('school', $school);

        if ($user) {
            $queryBuilder
                ->andWhere('u.id != :id')
                ->setParameter('id', $user->getId());
        }

        return array_map(
            function($row) {
                return $row['username'];
            },
            $queryBuilder
                ->getQuery()
                ->getArrayResult()
        );
    }
}
