<?php


namespace Fairpay\Bundle\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class PermissionListener implements EventSubscriberInterface
{
    /** @var  AuthorizationChecker */
    private $authorizationChecker;

    /**
     * PermissionListener constructor.
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if (!$configuration = $request->attributes->get('_permission')) {
            return;
        }

        $vendor = null;

        if ('_' === $configuration->getRole()[0]) {
            $vendor = $request->attributes->get('vendor');
        }

        if (!$this->authorizationChecker->isGranted($configuration->getRole(), $vendor)) {
            throw new AccessDeniedHttpException('Vous n\'avez pas les droits nécéssaires pour accéder à cette page.');
        }
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => ['onKernelController', -1]);
    }
}