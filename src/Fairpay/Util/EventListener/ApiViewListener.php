<?php


namespace Fairpay\Util\EventListener;


use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ApiViewListener
{
    private $baseHost;

    /** @var  Serializer */
    private $serializer;

    /**
     * ApiViewListener constructor.
     * @param Serializer $serializer
     * @param            $baseHost
     */
    public function __construct(Serializer $serializer, $baseHost)
    {
        $this->serializer = $serializer;
        $this->baseHost   = $baseHost;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ('api.' . $this->baseHost === $event->getRequest()->getHttpHost()) {
            $data = $event->getControllerResult();
            $event->setResponse(new JsonResponse($this->serializer->toArray($data)));
        }
    }
}