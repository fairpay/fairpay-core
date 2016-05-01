<?php


namespace Fairpay\Bundle\UserBundle\Tests\Security\Voter;


use Fairpay\Bundle\UserBundle\Entity\User;
use Fairpay\Bundle\UserBundle\Security\Acl\MaskBuilder;
use Fairpay\Bundle\UserBundle\Security\Voter\PermissionVoter;
use Fairpay\Util\Tests\UnitTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoterTest extends UnitTestCase
{
    /** @var  PermissionVoter */
    private $voter;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->voter = new PermissionVoter();
    }

    public function voteProvider()
    {
        return array(
            [['global' => 0], 'STUDENTS_MANAGE', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'SUBSCRIBERS_MANAGE', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'SETTINGS_MANAGE', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'ADMINS_MANAGE', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'EVENTS_MANAGE', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'ACCOUNTS_VIEW', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'VENDORS_VIEW', null, Voter::ACCESS_DENIED],
            [['global' => 0], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_STUDENTS_MANAGE], 'STUDENTS_MANAGE', null, Voter::ACCESS_GRANTED],

            [['global' => MaskBuilder::MASK_ACCOUNTS_VIEW], 'ACCOUNTS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_ACCOUNTS_VIEW], 'ACCOUNTS_MANAGE', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_ACCOUNTS_MANAGE], 'ACCOUNTS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_ACCOUNTS_MANAGE], 'ACCOUNTS_MANAGE', null, Voter::ACCESS_GRANTED],

            [['global' => MaskBuilder::MASK_VENDORS_VIEW], 'VENDORS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_VENDORS_VIEW], 'VENDORS_MANAGE', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_VENDORS_MANAGE], 'VENDORS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_VENDORS_MANAGE], 'VENDORS_MANAGE', null, Voter::ACCESS_GRANTED],

            [['global' => MaskBuilder::MASK_TRANSACTIONS_VIEW], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VIEW], 'TRANSACTIONS_DEPOSIT', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VIEW], 'TRANSACTIONS_WITHDRAWAL', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VIEW], 'TRANSACTIONS_VENDOR', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VIEW], 'TRANSACTIONS_GOD', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_TRANSACTIONS_DEPOSIT], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_DEPOSIT], 'TRANSACTIONS_DEPOSIT', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_DEPOSIT], 'TRANSACTIONS_WITHDRAWAL', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_DEPOSIT], 'TRANSACTIONS_VENDOR', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_DEPOSIT], 'TRANSACTIONS_GOD', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_TRANSACTIONS_WITHDRAWAL], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_WITHDRAWAL], 'TRANSACTIONS_DEPOSIT', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_WITHDRAWAL], 'TRANSACTIONS_WITHDRAWAL', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_WITHDRAWAL], 'TRANSACTIONS_VENDOR', null, Voter::ACCESS_DENIED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_WITHDRAWAL], 'TRANSACTIONS_GOD', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], 'TRANSACTIONS_DEPOSIT', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], 'TRANSACTIONS_WITHDRAWAL', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], 'TRANSACTIONS_VENDOR', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], 'TRANSACTIONS_GOD', null, Voter::ACCESS_DENIED],

            [['global' => MaskBuilder::MASK_TRANSACTIONS_GOD], 'TRANSACTIONS_VIEW', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_GOD], 'TRANSACTIONS_DEPOSIT', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_GOD], 'TRANSACTIONS_WITHDRAWAL', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_GOD], 'TRANSACTIONS_VENDOR', null, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_GOD], 'TRANSACTIONS_GOD', null, Voter::ACCESS_GRANTED],

            [['global' => 0], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_DENIED],
            [['global' => 0, 1 => 0], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_DENIED],
            [['global' => 0, 2 => MaskBuilder::MASK__TRANSACTIONS_VIEW], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_DENIED],
            [['global' => 0, 1 => MaskBuilder::MASK__TRANSACTIONS_VIEW], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_GRANTED],
            [['global' => 0, 1 => MaskBuilder::MASK__TRANSACTIONS_EXECUTE], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_GRANTED],
            [['global' => 0, 1 => MaskBuilder::MASK_TRANSACTIONS_VENDOR], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_GRANTED],
            [['global' => MaskBuilder::MASK_TRANSACTIONS_VENDOR, 1 => 0], '_TRANSACTIONS_VIEW', 1, Voter::ACCESS_GRANTED],
        );
    }

    /**
     * @dataProvider voteProvider
     * @param array $permissions
     * @param       $attribute
     * @param       $vendorId
     * @param       $shouldBeGranted
     */
    public function testVote(array $permissions, $attribute, $vendorId, $shouldBeGranted)
    {
        $user = new User();
        $user->setPermissions($permissions);

        $token = new UsernamePasswordToken($user, null, 'provider.key');

        $vendor = null;

        if ($vendorId) {
            $vendor = new User();
            $vendor->setId($vendorId);
        }

        $this->assertEquals($shouldBeGranted, $this->voter->vote($token, $vendor, [$attribute]));
    }
}