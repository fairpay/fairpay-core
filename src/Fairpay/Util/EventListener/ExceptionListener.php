<?php


namespace Fairpay\Util\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    private $baseHost;

    const INVALID_TOKEN = 1;

    static public $errorMessages = array(
        self::INVALID_TOKEN => 'invalid_token',
        400 => 'bad_request',
        401 => 'unauthorized',
        403 => 'forbidden',
        404 => 'not_found',
        500 => 'internal_server_error',
    );

    /**
     * ExceptionListener constructor.
     * @param $baseHost
     */
    public function __construct($baseHost)
    {
        $this->baseHost = $baseHost;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ('api.' . $this->baseHost === $event->getRequest()->getHttpHost()) {
            $e = $event->getException();
            $status = 500;

            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
            }

            $errorCode = $e->getCode() ? $e->getCode() : $status;
            $error = isset(self::$errorMessages[$errorCode]) ? self::$errorMessages[$errorCode] : 'error';

            $response = new JsonResponse(array(
                'status' => $status,
                'error' => $error,
                'message' => $e->getMessage(),
            ));

            $response->headers->set('X-Status-Code', $status);

            $event->setResponse($response);
        }
    }
}