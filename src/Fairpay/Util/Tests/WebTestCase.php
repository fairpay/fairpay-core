<?php


namespace Fairpay\Util\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Fairpay\Util\Tests\Helpers\FillFormHelper;
use Fairpay\Util\Tests\Helpers\MailHelper;
use Fairpay\Util\Tests\Helpers\RedirectedHelper;
use Fairpay\Util\Tests\Helpers\TestCaseHelper;
use Fairpay\Util\Tests\Helpers\UrlHelper;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property FillFormHelper $fillForm
 * @property UrlHelper $url
 * @property MailHelper $mail
 * @property RedirectedHelper $redirected
 */
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
        $tool->dropDatabase();
        $tool->createSchema($metadata);

        parent::setUp();
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