<?php


namespace Fairpay\Util\Manager;

use Doctrine\ORM\EntityManager as DoctrineEM;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;

abstract class EntityManager
{
    /**
     * @var DoctrineEM
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repo;

    /**
     * @var TraceableEventDispatcher
     */
    protected $dispatcher;

    /**
     * @param DoctrineEM               $em
     * @param TraceableEventDispatcher $dispatcher
     */
    public function __construct(DoctrineEM $em, TraceableEventDispatcher $dispatcher)
    {
        $this->em = $em;
        $this->repo = $em->getRepository($this->getEntityShortcutName());
        $this->dispatcher = $dispatcher;
    }

    abstract public function getEntityShortcutName();
}