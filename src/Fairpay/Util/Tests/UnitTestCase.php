<?php


namespace Fairpay\Util\Tests;


use Doctrine\ORM\EntityManager as DoctrineManager;
use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Util\Manager\CurrentSchoolAwareManager;
use Fairpay\Util\Manager\EntityManager;
use Prophecy\Argument;
use Prophecy\Prophet;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;

abstract class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var  Prophet */
    private $prophet;
    // Mocked
    protected $em;
    protected $repo;
    protected $dispatcher;
    protected $schoolManager;

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    protected function mock($class)
    {
        return $this->prophet->prophesize($class);
    }

    protected function mockEntityManager()
    {
        return $this->mock(DoctrineManager::class);
    }

    protected function mockDispatcher()
    {
        return $this->mock(TraceableEventDispatcher::class);
    }

    protected function initManager(EntityManager $manager)
    {
        // Inject Doctrine and Event Dispatcher
        $this->em = $this->mockEntityManager();
        $this->dispatcher = $this->mockDispatcher();
        $manager->init($this->em->reveal(), $this->dispatcher->reveal());

        // Mock repository
        $this->repo = $this->mock($this->getRepoClass($manager));
        $this->em->getRepository($manager->getEntityShortcutName())->willReturn($this->repo->reveal());

        if ($manager instanceof CurrentSchoolAwareManager) {
            // Inject School Manager
            $this->schoolManager = $this->mock(SchoolManager::class);
            $this->schoolManager->getCurrentSchool()->willReturn(null);
            $manager->setSchoolManager($this->schoolManager->reveal());
        }
    }

    protected function getRepoClass(EntityManager $manager)
    {
        preg_match('/([A-Z][a-z]+)(\w+)Bundle:(\w+)/', $manager->getEntityShortcutName(), $matches);

        return sprintf('%s\Bundle\%sBundle\Repository\%sRepository', $matches[1], $matches[2], $matches[3]);
    }

    protected function shouldBePersisted($class)
    {
        $this->em->persist(Argument::type($class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
    }

    /**
     * Current School is set.
     */
    protected function havingASchool()
    {
        $this->schoolManager->getCurrentSchool()->willReturn(new School());
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }
}