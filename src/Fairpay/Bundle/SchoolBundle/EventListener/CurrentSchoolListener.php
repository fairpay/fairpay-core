<?php


namespace Fairpay\Bundle\SchoolBundle\EventListener;


use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handle subdomain related work.
 */
class CurrentSchoolListener
{
    /** @var  SchoolManager */
    private $schoolManager;

    private $baseHost;

    /** @var  RouterInterface */
    private $router;

    /**
     * CurrentSchoolListener constructor.
     * @param SchoolManager   $schoolManager
     * @param string          $baseHost
     * @param RouterInterface $router
     */
    public function __construct(SchoolManager $schoolManager, $baseHost, RouterInterface $router)
    {
        $this->schoolManager = $schoolManager;
        $this->baseHost = $baseHost;
        $this->router = $router;
    }

    /**
     * Get the school from the subdomain and make it accessible via the SchoolManager and sets the _subdomain parameter
     * in the router context.
     * @param GetResponseEvent $event
     * @throws \Fairpay\Bundle\SchoolBundle\Manager\CurrentSchoolAlreadySetException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->schoolManager->getCurrentSchool()) {
            return;
        }

        $slug = $this->getSubdomain($event->getRequest());

        // We don't want to affect showcase or api pages
        if ($slug && $this->schoolManager->isValidSlug($slug)) {
            $school = $this->schoolManager->setCurrentSchool($slug);
            $this->router->getContext()->setParameter('_subdomain', $slug);

            if (!$school) {
                throw new NotFoundHttpException("Subdomain $slug does not match any School's slug.");
            }
        }
    }

    /**
     * Get the subdomain from the request.
     * @param Request $request
     * @return string|null
     */
    public function getSubdomain(Request $request)
    {
        if (false === strpos($request->getHost(), $this->baseHost)) {
            return null;
        }

        return preg_replace('#\.?' . $this->baseHost . '$#', '', $request->getHost());
    }
}