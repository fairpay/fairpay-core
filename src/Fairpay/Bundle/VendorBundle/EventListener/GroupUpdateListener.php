<?php


namespace Fairpay\Bundle\VendorBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Fairpay\Bundle\VendorBundle\Entity\Group;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * When a Group is updated this listener updates the users as well.
 *
 * - When users are removed from the Group, their permission regarding this particular vendor is removed
 * - When users are added to the Group, their permission regarding this particular vendor is updated
 * - When the mask of the Group changes, all the users permission regarding this particular vendor are updated
 */
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

        $userManager = $this->container->get('user_manager');
        $changes = $args
            ->getEntityManager()
            ->getUnitOfWork()
            ->getEntityChangeSet($group);

        if (key_exists('users', $changes)) {
            $newUsers     = array_diff($changes['users'][1], $changes['users'][0]);
            $removedUsers = array_diff($changes['users'][0], $changes['users'][1]);

            foreach ($removedUsers as $id) {
                $user = $userManager->findUserById($id);
                $userManager->removePermission($user, $group->getVendor());
            }

            foreach ($newUsers as $id) {
                $user = $userManager->findUserById($id);
                $userManager->setPermission($user, $group);
            }
        }

        if (key_exists('mask', $changes)) {
            foreach ($group->getUsers() as $id) {
                $user = $userManager->findUserById($id);
                $userManager->setPermission($user, $group);
            }
        }
    }
}