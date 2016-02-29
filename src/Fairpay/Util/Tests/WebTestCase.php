<?php


namespace Fairpay\Util\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Fairpay\Util\Tests\Helpers\TestCaseHelper;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class WebTestCase extends BaseTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    /** @var  Client */
    public $client;

    /** @var  ContainerInterface */
    public $container;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->client = static::createClient();

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->createSchema($metadata);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
    }

    /**
     * Lazy load form helpers.
     *
     * @param $name
     * @return TestCaseHelper
     */
    public function __get($name)
    {
        $helper = __NAMESPACE__ . '\Helpers\\' . ucfirst($name) . 'Helper';
        $this->$name = new $helper($this);
        return $this->$name;
    }
}