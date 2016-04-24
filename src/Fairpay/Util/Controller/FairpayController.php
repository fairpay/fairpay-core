<?php


namespace Fairpay\Util\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Every Controller should extend this class.
 */
class FairpayController extends Controller
{
    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @param string $entityName
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($entityName)
    {
        return $this->em()->getRepository($entityName);
    }

    protected function flashSuccess($message)
    {
        $this->addFlash('success', $message);
    }

    protected function flashError($message)
    {
        $this->addFlash('danger', $message);
    }

    /**
     * Throws an exception unless the attributes are granted against the current authentication token and optionally
     * supplied object.
     *
     * @param mixed  $attributes The attributes
     * @param mixed  $object     The object
     * @param string $message    The message passed to the exception
     *
     * @throws AccessDeniedHttpException
     */
    protected function denyAccessUnlessGranted($attributes, $object = null, $message = 'Vous n\'avez pas les droits nécéssaire pour effectuer cette opération.')
    {
        if (!$this->isGranted($attributes, $object)) {
            throw new AccessDeniedHttpException($message);
        }
    }
}