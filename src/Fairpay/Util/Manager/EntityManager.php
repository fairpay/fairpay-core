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
    public function init(DoctrineEM $em, TraceableEventDispatcher $dispatcher)
    {
        $this->em = $em;
        $this->repo = $em->getRepository($this->getEntityShortcutName());
        $this->dispatcher = $dispatcher;
    }

    /**
     * Itterate through all $source public attributes and call corresponding setter of $target.
     * @param object $target
     * @param object $source
     */
    protected function mapData($target, $source)
    {
        foreach (get_object_vars($source) as $field => $value) {
            $target->{'set' . ucfirst($field)}($value);
        }
    }

    /**
     * This method should return the entity shortcut name to properly get the repository.
     *
     * @return string
     */
    abstract public function getEntityShortcutName();
}