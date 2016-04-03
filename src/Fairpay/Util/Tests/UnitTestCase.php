<?php


namespace Fairpay\Util\Tests;


use Prophecy\Prophet;

class UnitTestCase extends \PHPUnit_Framework_TestCase
{
    const doctrine_orm_entity_manager = 'Doctrine\ORM\EntityManager';
    const event_dispatcher = 'Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher';
    const security_token_storage = 'Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage';
    const school_manager = 'Fairpay\Bundle\SchoolBundle\Manager\SchoolManager';

    /** @var  Prophet */
    private $prophet;

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    public function mock($class)
    {
        return $this->prophet->prophesize($class);
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }
}