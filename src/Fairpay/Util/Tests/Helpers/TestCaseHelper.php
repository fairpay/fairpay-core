<?php


namespace Fairpay\Util\Tests\Helpers;


use Fairpay\Util\Tests\WebTestCase;

abstract class TestCaseHelper
{
    /** @var  WebTestCase */
    protected $testCase;

    /**
     * TestCaseHelper constructor.
     * @param WebTestCase $testCase
     */
    public function __construct(WebTestCase $testCase)
    {
        $this->testCase = $testCase;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getClient()
    {
        return $this->testCase->client;
    }

    /**
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getCrawler()
    {
        return $this->testCase->client->getCrawler();
    }

    /**
     * @param string $service
     * @return object
     */
    protected function get($service)
    {
        return $this->testCase->container->get($service);
    }
}