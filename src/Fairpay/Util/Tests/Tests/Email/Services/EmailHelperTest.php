<?php


namespace Fairpay\Util\Tests\Tests\Email\Services;


use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Util\Email\Services\EmailHelper;

class EmailHelperTest extends \PHPUnit_Framework_TestCase
{
    /**  @var EmailHelper */
    static private $emailHelper;

    static private $disposableDomains = array(
        'yopmail.com'
    );

    static private $standardDomains = array(
        'gmail'
    );

    static public function setUpBeforeClass()
    {
        self::$emailHelper = new EmailHelper(self::$disposableDomains, self::$standardDomains);
    }

    public function getEmailProvider()
    {
        $school = new School();
        $school->setEmail('addr@domain.com');

        return array(
            ['addr@domain.com', 'addr@domain.com'],
            [$school, 'addr@domain.com'],
            [null, null],
            [42, new \InvalidArgumentException()],
            [new \stdClass(), new \InvalidArgumentException()],
        );
    }

    public function getDomainProvider()
    {
        return array(
            ['addr@gmail.com', 'gmail.com'],
            ['hotmail.fr', 'hotmail.fr'],
        );
    }

    public function getMainDomainProvider()
    {
        return array(
            ['gmail.com', 'gmail'],
            ['addr@gmail.com', 'gmail'],
            ['hotmail.co.uk', 'hotmail'],
            ['addr@hotmail.co.uk', 'hotmail'],
        );
    }

    public function isValidDomainProvider()
    {
        return array(
            ['gmail.com', true],
            ['addr@hotmail.fr', true],
            ['niglo123.co.uk', true],
            ['addr@la-poste.info', true],

            ['domain.abcde', false],
            ['domain.x', false],
            ['dom..ain.fr', false],
            ['dom--ain.fr', false],
            ['1domain.fr', false],
        );
    }

    public function isStandardDomainProvider()
    {
        return array(
            ['addr@gmail.com', true],
            ['addr@gmail.aa.bb.cc', true],
            ['addr@edu.esiee.fr', false],
        );
    }

    public function isDisposableDomainProvider()
    {
        return array(
            ['addr@yopmail.com', true],
            ['addr@yopmail.aa.bb.cc', false],
            ['addr@gmail.com', false],
            ['addr@edu.esiee.fr', false],
        );
    }

    /**
     * @dataProvider getEmailProvider
     * @param string|object $email
     * @param string        $expected
     * @throws \Exception
     */
    public function testGetEmail($email, $expected)
    {
        if ($expected instanceof \Exception) {
            try {
                self::$emailHelper->getEmail($email);
                $this->fail('It should have thrown an exception.');
            } catch (\Exception $e) {
                if (get_class($expected) !== get_class($e)) {
                    throw $e;
                }
            }
        } else {
            $this->assertEquals($expected, self::$emailHelper->getEmail($email));
        }
    }

    /**
     * @dataProvider getDomainProvider
     * @param $email
     * @param $expected
     */
    public function testGetDomain($email, $expected)
    {
        $this->assertEquals($expected, self::$emailHelper->getDomain($email));
    }

    /**
     * @dataProvider getMainDomainProvider
     * @param $email
     * @param $expected
     */
    public function testGetMainDomain($email, $expected)
    {
        $this->assertEquals($expected, self::$emailHelper->getMainDomain($email));
    }

    /**
     * @dataProvider isValidDomainProvider
     * @param $email
     * @param $expected
     */
    public function testIsValidDomain($email, $expected)
    {
        $this->assertEquals($expected, self::$emailHelper->isValidDomain($email));
    }

    /**
     * @dataProvider isStandardDomainProvider
     * @param $email
     * @param $expected
     */
    public function testIsStandardDomain($email, $expected)
    {
        $this->assertEquals($expected, self::$emailHelper->isStandard($email));
    }

    /**
     * @dataProvider isDisposableDomainProvider
     * @param $email
     * @param $expected
     */
    public function testIsDisposableDomain($email, $expected)
    {
        $this->assertEquals($expected, self::$emailHelper->isDisposable($email));
    }
}