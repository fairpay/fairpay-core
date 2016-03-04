<?php


namespace Fairpay\Bundle\SchoolBundle\Tests\Manager;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Bundle\SchoolBundle\Manager\CurrentSchoolAlreadySetException;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Fairpay\Util\Tests\WebTestCase;

class SchoolManagerTest extends WebTestCase
{

    /** @var  SchoolManager */
    public $schoolManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->schoolManager = $this->container->get('school_manager');
    }

    public function isValidSlugProvider()
    {
        return [[array(
            ['esiee', true],
            ['esiee2', true],
            ['esiee-paris', true],

            ['1esiee', false],
            ['esiee.paris', false],
            ['esiee--paris', false],
            ['api', false],
            ['www', false],
        )]];
    }

    public function guessEmailPolicyProvider()
    {
        return [[array(
            ['bde@gmail.com', false, null],
            ['bde@edu.esiee.fr', true, ['edu.esiee.fr']],
        )]];
    }

    /**
     * @dataProvider isValidSlugProvider
     * @param $data
     */
    public function testIsValidSlug(array $data)
    {
        foreach ($data as list($slug, $expected)) {
            $this->assertEquals($expected, $this->schoolManager->isValidSlug($slug));
        }
    }

    /**
     * @dataProvider guessEmailPolicyProvider
     * @param $data
     */
    public function testGuessEmailPolicy($data)
    {
        foreach ($data as list($email, $allowUnregisteredEmails, $allowedEmailDomains)) {
            $school = new School();
            $school->setEmail($email);;
            $this->schoolManager->guessEmailPolicy($school);
            $this->assertEquals($allowUnregisteredEmails, $school->getAllowUnregisteredEmails());
            $this->assertEquals($allowedEmailDomains, $school->getAllowedEmailDomains());
        }
    }

    public function testSetCurrentSchool()
    {
        $this->havingSchoolRegistered();

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

    /**
     * Make sure that a school is registered.
     */
    protected function havingSchoolRegistered()
    {
        $school = new School('ESIEE Paris', 'bde@edu.esiee.fr');
        $school->setSlug('esiee');

        $this->em->persist($school);
        $this->em->flush();
    }
}