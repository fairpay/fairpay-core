<?php


namespace Fairpay\Bundle\SchoolBundle\Tests\EventListener;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\EventListener\CurrentSchoolListener;
use Fairpay\Util\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CurrentSchoolListenerTest extends UnitTestCase
{
    const router          = 'Symfony\Bundle\FrameworkBundle\Routing\Router';
    const event           = 'Symfony\Component\HttpKernel\Event\GetResponseEvent';
    const request_context = 'Symfony\Component\Routing\RequestContext';
    const request         = 'Symfony\Component\HttpFoundation\Request';

    /** @var  CurrentSchoolListener */
    private $currentSchoolListener;
    private $schoolManager;
    private $router;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->schoolManager = $this->mock(self::school_manager);
        $this->router = $this->mock(self::router);
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

    public function listenerProvider()
    {
        return[
            ['esiee.fairpay.local', true],
            ['api.fairpay.local', false],
            ['fake.fairpay.local', false],
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

    /**
     * @dataProvider listenerProvider
     * @param $host
     * @param $shouldWork
     */
    public function testListener($host, $shouldWork)
    {
        // Event
        $event = $this->mock(self::event);
        $event->getRequest()->willReturn($this->getRequest($host));

        // School manager
        $this->schoolManager->getCurrentSchool()->willReturn(null);

        if (true === $shouldWork) {
            $this->schoolManager->isValidSlug(Argument::any())->willReturn(true);
            $this->schoolManager->setCurrentSchool(Argument::any())->shouldBeCalled()->willReturn(new School());
        } else if (false === $shouldWork) {
            $this->schoolManager->isValidSlug(Argument::any())->willReturn(false);
            $this->schoolManager->setCurrentSchool(Argument::any())->shouldNotBeCalled();
        } else {
            $this->schoolManager->isValidSlug(Argument::any())->willReturn(true);
            $this->schoolManager->setCurrentSchool(Argument::any())->shouldBeCalled()->willReturn(null);
        }

        if (false !== $shouldWork) {
            // Context
            $context = $this->mock(self::request_context);
            $context->setParameter('_subdomain', Argument::any())->shouldBeCalled();

            // Router
            $this->router->getContext()->willReturn($context->reveal());
        }

        // Test
        try {
            $this->currentSchoolListener->onKernelRequest($event->reveal());

            if ($shouldWork instanceof NotFoundHttpException) {
                $this->fail('An NotFoundHttpException should have been thrown.');
            }
        } catch(NotFoundHttpException $e) {}
    }

    /**
     * @param $host
     * @return Request
     */
    private function getRequest($host)
    {
        $request = $this->mock(self::request);
        $request->getHost()->willReturn($host);
        return $request->reveal();
    }
}