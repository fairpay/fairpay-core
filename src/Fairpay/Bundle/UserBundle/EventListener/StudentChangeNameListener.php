<?php


namespace Fairpay\Bundle\UserBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Fairpay\Bundle\StudentBundle\Entity\Student;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * When a student updates his first or last name, update the user's display name and username.
 */
class StudentChangeNameListener
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
        $student = $args->getEntity();

        if (!$student instanceof Student || !$student->getUser()) {
            return;
        }

        $user = $student->getUser();
        $displayName = (string) $student;
        $user->setDisplayName($displayName);
        $user->setUsername($this->container->get('user_manager')->usernameFromDisplayName($displayName, $user));

        $args->getEntityManager()->persist($user);
        $args->getEntityManager()->flush();
    }
}