<?php


namespace Fairpay\Bundle\UserBundle\Tests\Security\Acl;


use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder as MB;
use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder;
use Fairpay\Util\Tests\UnitTestCase;

class MaskBuilderTest extends UnitTestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnknownMask()
    {
        $builder = new MB();
        $builder->resolveMask('FAKE');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidType()
    {
        $builder = new MB();
        $builder->resolveMask(true);
    }

    public function testResolveMask()
    {
        $builder = new MB();
        $this->assertEquals(MB::MASK_TRANSACTIONS_VIEW, $builder->resolveMask('TRANSACTIONS_VIEW'));
    }

    public function vendorSpecificProvider()
    {
        return array(
            [MB::MASK__TRANSACTIONS_VIEW, true],
            [MB::MASK__ADMINS_MANAGE, true],

            [MB::MASK_STUDENTS_MANAGE, false],
            [MB::MASK_TRANSACTIONS_GOD, false],
        );
    }

    /**
     * @dataProvider vendorSpecificProvider
     * @param $permission
     * @param $isVendorSpecific
     */
    public function testIsVendorSpecific($permission, $isVendorSpecific)
    {
        $builder = new MB($permission);
        $this->assertEquals($isVendorSpecific, $builder->isVendorSpecific());
    }

    public function includesProvider()
    {
        return array(
            [MB::MASK__TRANSACTIONS_VIEW, MB::MASK__TRANSACTIONS_VIEW, true],
            [MB::MASK__TRANSACTIONS_VIEW | MB::MASK__EVENTS_VIEW, MB::MASK__TRANSACTIONS_VIEW, true],
            [MB::MASK__TRANSACTIONS_EXECUTE, MB::MASK__TRANSACTIONS_VIEW, true],
            [MB::MASK__TRANSACTIONS_VIEW, MB::MASK__TRANSACTIONS_EXECUTE, false],
        );
    }

    /**
     * @dataProvider includesProvider
     * @param $permission
     * @param $mask
     * @param $shouldInclude
     */
    public function testIncludes($permission, $mask, $shouldInclude)
    {
        $builder = new MB($permission);
        $this->assertEquals($shouldInclude, $builder->includes($mask));
    }

    public function isIncludedProvider()
    {
        return array(
            [MB::MASK__TRANSACTIONS_VIEW, MB::MASK__TRANSACTIONS_VIEW, true],
            [MB::MASK__TRANSACTIONS_VIEW | MB::MASK__EVENTS_VIEW, MB::MASK__TRANSACTIONS_VIEW, false],
            [MB::MASK__TRANSACTIONS_VIEW, MB::MASK__TRANSACTIONS_VIEW | MB::MASK__EVENTS_VIEW, true],
            [MB::MASK__TRANSACTIONS_EXECUTE, MB::MASK__TRANSACTIONS_VIEW, false],
            [MB::MASK__TRANSACTIONS_VIEW, MB::MASK__TRANSACTIONS_EXECUTE, true],
        );
    }

    /**
     * @dataProvider isIncludedProvider
     * @param $permission
     * @param $mask
     * @param $shouldInclude
     */
    public function testIsIncluded($permission, $mask, $shouldInclude)
    {
        $builder = new MB($permission);
        $this->assertEquals($shouldInclude, $builder->isIncluded($mask));
    }

    public function testAddAllGlobalRoles()
    {
        $builder = new MaskBuilder();
        $builder->addAllGlobalRoles(MB::MASK_STUDENTS_MANAGE | MB::MASK_EVENTS_MANAGE | MB::MASK__TRANSACTIONS_VIEW | MB::MASK__SETTINGS_MANAGE);

        $this->assertTrue($builder->includes(MB::MASK_STUDENTS_MANAGE | MB::MASK_EVENTS_MANAGE));
        $this->assertFalse($builder->includes(MB::MASK__TRANSACTIONS_VIEW));
        $this->assertFalse($builder->includes(MB::MASK__SETTINGS_MANAGE));
    }
}