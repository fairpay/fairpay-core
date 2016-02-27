<?php


namespace Fairpay\Util\Manager;

use Doctrine\ORM\EntityManager as DoctrineEM;
use Doctrine\ORM\EntityRepository;

abstract class EntityManager
{
    /**
     * @var DoctrineEM
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repo;

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