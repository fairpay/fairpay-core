<?php


namespace Fairpay\Util\Manager;

use Doctrine\ORM\EntityManager as DoctrineEM;
use Doctrine\ORM\EntityRepository;

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
     * @param DoctrineEM $em
     */
    public function __construct(DoctrineEM $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository($this->getEntityShortcutName());
    }

    abstract public function getEntityShortcutName();
}