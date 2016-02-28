<?php


namespace Fairpay\Util\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}