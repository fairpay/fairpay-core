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
    private $repo;

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
     * Lazy load repository.
     * @return EntityRepository
     */
    public function getRepo()
    {
        if (!$this->repo) {
            $this->repo = $this->em->getRepository($this->getEntityShortcutName());
        }

        return $this->repo;
    }

    /**
     * This method should return the entity shortcut name to properly get the repository.
     *
     * @return string
     */
    abstract public function getEntityShortcutName();
}