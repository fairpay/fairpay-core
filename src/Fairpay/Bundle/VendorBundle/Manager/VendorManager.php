<?php


namespace Fairpay\Bundle\VendorBundle\Manager;


use Doctrine\ORM\EntityManager;
use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Event\UserCreatedEvent;
use Fairpay\Bundle\UserBundle\Manager\UserManager;
use Fairpay\Bundle\VendorBundle\Entity\Group;
use Fairpay\Bundle\VendorBundle\Form\GroupAssignment;
use Fairpay\Bundle\VendorBundle\Form\GroupData;
use Fairpay\Bundle\VendorBundle\Form\VendorData;

class VendorManager
{
    /** @var  UserManager */
    private $userManager;

    /** @var  EntityManager */
    private $em;

    /**
     * VendorManager constructor.
     * @param UserManager   $userManager
     * @param EntityManager $em
     */
    public function __construct(UserManager $userManager, EntityManager $em)
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    public function createMain($displayName, $email, $plainPassword)
    {
        return $this->userManager->createVendor($displayName, $email, $plainPassword, UserCreatedEvent::REGISTERED_WITH_SCHOOL);
    }

    public function create(VendorData $data)
    {
        return $this->userManager->createVendor($data->name, $data->email);
    }

    public function addGroup(User $vendor, GroupData $groupData)
    {
        $group = new Group($groupData->name, $groupData->permissions, $vendor);

        $this->em->persist($group);
        $this->em->flush();

        return $group;
    }

    /**
     * Assign a group to a user
     * @param User            $vendor
     * @param GroupAssignment $assignment
     */
    public function assignGroup(User $vendor, GroupAssignment $assignment)
    {
        $grpRepo = $this->em->getRepository('FairpayVendorBundle:Group');

        if ($group = $grpRepo->findByUserAndVendor($assignment->user, $vendor)) {
            $group->removeUser($assignment->user);

            $this->em->persist($group);
            $this->em->flush();
        }

        $assignment->group->addUser($assignment->user);
        $this->em->persist($assignment->group);
        $this->em->flush();
    }

    /**
     * Remove a $user from any group he might be in related to the $vendor.
     *
     * @param User         $vendor
     * @param User|integer $user
     */
    public function removeUserFromAdmins(User $vendor, $user)
    {
        if (!$user instanceof User) {
            $user = $this->em->getRepository('FairpayUserBundle:User')->findOneBy(['id' => $user]);
        }

        foreach ($vendor->getGroups() as $group) {
            if (in_array($user->getId(), $group->getUsers())) {
                $group->removeUser($user);
                $this->em->persist($group);
            }
        }

        $this->em->flush();
    }
}