<?php


namespace Fairpay\Bundle\SchoolBundle\Tests\EventListener;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\EventListener\CurrentSchoolListener;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Util\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RequestContext;

class CurrentSchoolListenerTest extends UnitTestCase
{
    /** @var  CurrentSchoolListener */
    private $currentSchoolListener;
    private $router;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->schoolManager = $this->mock(SchoolManager::class);
        $this->router        = $this->mock(Router::class);

        $this->currentSchoolListener = new CurrentSchoolListener(
            $this->schoolManager->reveal(),
            'fairpay.local',
            $this->router->reveal()
        );
    }

    public function getSubdomainProvider()
    {
        return [
            ['google.com', null],
            ['fairpay.local.com', null],
            ['fairpay.local', ''],
            ['sub.fairpay.local', 'sub'],
            ['sub.domain.fairpay.local', 'sub.domain'],
        ];
    }

    /**
     * @dataProvider getSubdomainProvider
     * @param $host
     * @param $expected
     */
    public function testGetSubdomain($host, $expected)
    {
        $this->assertEquals($expected, $this->currentSchoolListener->getSubdomain($this->getRequest($host)));
    }

    public function listenerProvider()
    {
        return [
            ['esiee.fairpay.local', true, true, false],
            ['api.fairpay.local', false, false, false],
            ['fake.fairpay.local', true, false, true],
        ];
    }

    /**
     * @dataProvider listenerProvider
     * @param $host
     * @param $validSlug
     * @param $schoolExist
     * @param $shouldThrow
     */
    public function testListener($host, $validSlug, $schoolExist, $shouldThrow)
    {
        // Event
        $event = $this->mock(GetResponseEvent::class);
        $event->getRequest()->willReturn($this->getRequest($host));

        // School manager
        $this->schoolManager->getCurrentSchool()->willReturn(null);
        $this->schoolManager->isValidSlug(Argument::any())->willReturn($validSlug);

        if ($validSlug) {
            $this->schoolManager->setCurrentSchool(Argument::any())->shouldBeCalled()->willReturn($schoolExist ? new School() : null);

            // Context
            $context = $this->mock(RequestContext::class);
            $context->setParameter('_subdomain', Argument::any())->shouldBeCalled();

            // Router
            $this->router->getContext()->willReturn($context->reveal());
        } else {
            $this->schoolManager->setCurrentSchool(Argument::any())->shouldNotBeCalled();
        }

        // Test
        try {
            $this->currentSchoolListener->onKernelRequest($event->reveal());

            if ($shouldThrow) {
                $this->fail('An NotFoundHttpException should have been thrown.');
            }
        } catch (NotFoundHttpException $e) {
            if (!$shouldThrow) {
                $this->fail('An NotFoundHttpException should not have been thrown.');
            }
        }
    }

    /**
     * @param $host
     * @return Request
     */
    private function getRequest($host)
    {
        $request = $this->mock(Request::class);
        $request->getHost()->willReturn($host);

        return $request->reveal();
    }
}