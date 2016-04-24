<?php


namespace Fairpay\Bundle\VendorBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fairpay\Bundle\VendorBundle\Entity\Group;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GroupUpdateListener
{
    /** @var  ContainerInterface */
    private $container;

    /**
     * StudentChangeNameListener constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $group = $args->getEntity();

        if (!$group instanceof Group) {
            return;
        }

        $usrRepo = $args->getEntityManager()->getRepository('FairpayUserBundle:User');
        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($group);
        $newUsers = array_diff($changes['users'][1], $changes['users'][0]);
        $removedUsers = array_diff($changes['users'][0], $changes['users'][1]);

        foreach ($removedUsers as $id) {
            $user = $usrRepo->findOneBy(['id' => $id]);
            $permissions = $user->getPermissions();

            unset($permissions[$group->getVendor()->getId()]);

            $user->setPermissions($permissions);
            $args->getEntityManager()->persist($user);
        }

        foreach ($newUsers as $id) {
            $user = $usrRepo->findOneBy(['id' => $id]);
            $permissions = $user->getPermissions();

            $permissions[$group->getVendor()->getId()] = $group->getMask();

            $user->setPermissions($permissions);
            $args->getEntityManager()->persist($user);
        }

        $args->getEntityManager()->flush();
    }
}