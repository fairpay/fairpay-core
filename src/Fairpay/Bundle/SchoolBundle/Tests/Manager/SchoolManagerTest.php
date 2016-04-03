<?php


namespace Fairpay\Bundle\SchoolBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Manager\CurrentSchoolAlreadySetException;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Util\Email\Services\EmailHelper;
use Fairpay\Util\Tests\UnitTestCase;
use Fairpay\Util\Util\TokenGenerator;
use Prophecy\Argument;

class SchoolManagerTest extends UnitTestCase
{
    const school_repository = 'Fairpay\Bundle\SchoolBundle\Repository\SchoolRepository';
    /** @var  SchoolManager */
    private $schoolManager;

    // Mocked
    private $em;
    private $repo;
    private $dispatcher;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->schoolManager = new SchoolManager(
            new EmailHelper([], ['gmail']),
            new TokenGenerator()
        );

        $this->em = $this->mock(self::doctrine_orm_entity_manager);
        $this->dispatcher = $this->mock(self::event_dispatcher);
        $this->schoolManager->init($this->em->reveal(), $this->dispatcher->reveal());

        $this->repo = $this->mock(self::school_repository);
        $this->em->getRepository(SchoolManager::ENTITY_SHORTCUT_NAME)->willReturn($this->repo->reveal());
    }

    public function isValidSlugProvider()
    {
        return [
            ['esiee', true],
            ['esiee2', true],
            ['esiee-paris', true],

            ['1esiee', false],
            ['esiee.paris', false],
            ['esiee--paris', false],
            ['api', false],
            ['www', false],
        ];
    }

    public function guessEmailPolicyProvider()
    {
        return [
            ['bde@gmail.com', false, null],
            ['bde@edu.esiee.fr', true, ['edu.esiee.fr']],
        ];
    }

    /**
     * @dataProvider isValidSlugProvider
     * @param $slug
     * @param $expected
     */
    public function testIsValidSlug($slug, $expected)
    {
        $this->assertEquals($expected, $this->schoolManager->isValidSlug($slug));
    }

    /**
     * @dataProvider guessEmailPolicyProvider
     * @param $email
     * @param $allowUnregisteredEmails
     * @param $allowedEmailDomains
     */
    public function testGuessEmailPolicy($email, $allowUnregisteredEmails, $allowedEmailDomains)
    {
            $school = new School();
            $school->setEmail($email);;
            $this->schoolManager->guessEmailPolicy($school);
            $this->assertEquals($allowUnregisteredEmails, $school->getAllowUnregisteredEmails());
            $this->assertEquals($allowedEmailDomains, $school->getAllowedEmailDomains());
    }

    public function testSetCurrentSchoolWithSlug()
    {
        $this->repo->findOneBy(['slug' => 'fake'])->willReturn(null);
        $this->repo->findOneBy(['slug' => 'esiee'])->willReturn(new School('ESIEE Paris'));

        // Try to set school with unknown slug
        $school = $this->schoolManager->setCurrentSchool('fake');
        $this->assertNull($school);

        // Set school with known slug
        $school = $this->schoolManager->setCurrentSchool('esiee');
        $this->assertEquals('ESIEE Paris', $school->getName());

        try {
            $this->schoolManager->setCurrentSchool('fake');
            $this->fail('Should not be able to set current school again.');
        } catch (CurrentSchoolAlreadySetException $e) {
            // Should not be able to set current school again
        }
    }

    public function testSetCurrentSchoolWithSchool()
    {
        $this->schoolManager->setCurrentSchool(new School('ESIEE Paris'));
        $school = $this->schoolManager->getCurrentSchool();

        $this->assertNotNull($school);
        $this->assertEquals('ESIEE Paris', $school->getName());
    }
}