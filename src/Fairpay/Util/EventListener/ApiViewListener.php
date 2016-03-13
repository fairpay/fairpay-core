<?php


namespace Fairpay\Util\EventListener;


use JMS\Serializer\Serializer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ApiViewListener implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array(
            'kernel.view' => array(
                array('onKernelView'),
            ),
            'kernel.request' => array(
                array('onKernelRequest', 64),
            ),
            'kernel.response' => array(
                array('onKernelResponse'),
            ),
        );
    }

    /**
     * Serialize data.
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($this->isApiCall($event->getRequest())) {
            $data = $event->getControllerResult();
            $event->setResponse(new JsonResponse($this->serializer->toArray($data)));
        }
    }

    /**
     * Add Access-Control-* headers for cors OPTIONS requests.
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isMethod('OPTIONS') && $this->isApiCall($request)) {
            $response = new Response('', 200, [
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => $request->headers->get('access-control-request-method', '*'),
                'Access-Control-Allow-Headers' => $request->headers->get('access-control-request-headers', '*'),
            ]);

            $event->setResponse($response);
        }
    }

    /**
     * Add Access-Control-Allow-Origin header to every response of the api.
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->isApiCall($event->getRequest())) {
            $response = $event->getResponse();
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
    }

    private function isApiCall(Request $request)
    {
        return 'api.' . $this->baseHost === $request->getHttpHost();
    }
}