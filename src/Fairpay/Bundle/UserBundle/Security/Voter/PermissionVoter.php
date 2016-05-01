<?php


namespace Fairpay\Bundle\UserBundle\Security\Voter;

use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        try {
            $builder = new MaskBuilder();
            $builder->resolveMask($attribute);

            return $subject === null ? true : $subject instanceof User;
        } catch(\InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute
     * @param User           $vendor
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $vendor, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        $builder = new MaskBuilder();
        $builder->set($builder->resolveMask($attribute));

        if (!$vendor || !key_exists($vendor->getId(), $user->getPermissions())) {
            return $builder->isIncluded($user->getPermissions()['global']);
        }

        $permission = new MaskBuilder($user->getPermissions()[$vendor->getId()]);
        $permission->add($user->getPermissions()['global']);

        return $builder->isIncluded($permission->get());
    }
}