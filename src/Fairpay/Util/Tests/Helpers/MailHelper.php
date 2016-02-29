<?php


namespace Fairpay\Util\Tests\Helpers;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class MailHelper extends TestCaseHelper
{
    /**
     * Enable client profiler.
     */
    public function catchMails()
    {
        $this->getClient()->enableProfiler();
    }

    /**
     * Returns the route parameter in email body.
     *
     * @param string $name Route name
     * @param string $param Parameter name
     * @return string
     */
    public function getLinkParam($name, $param)
    {
        $collector = $this->getCollector();
        $this->testCase->assertGreaterThanOrEqual(1, $collector->getMessageCount(), 'No email was sent.');
        $body = $collector->getMessages()[0]->getBody();

        /** @var Router $router */
        $router = $this->get('router');
        $route = $router->generate($name, array($param => md5($param)));
        $route = preg_replace('[#()]', '\\\$0', $route); // Escape regexp special char
        $route = str_replace(md5($param), '([^/]+)', $route);
        preg_match('#' . $route . '#', $body, $matches);

        $this->testCase->assertEquals(2, count($matches), "Link to route $name was not found in the email body.");
        return $matches[1];
    }

    /**
     * @return \Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector
     */
    protected function getCollector()
    {
        $profile = $this->testCase->client->getProfile();
        $this->testCase->assertNotEquals(false, $profile, 'Could not get Profile. Did you forget to enable Profiler?');
        return $profile->getCollector('swiftmailer');
    }
}