<?php


namespace Fairpay\Bundle\UserBundle\Security\Acl;



class MaskBuilder extends AbstractMaskBuilder
{
    const VENDOR_SPECIFIC_ROLES = 127;              // 1 << 0 | 1 << 1 | 1 << 2 | 1 << 3 | 1 << 4 | 1 << 5 | 1 << 6

    const MASK__TRANSACTIONS_VIEW = 1;              // 1 << 0
    const MASK__TRANSACTIONS_EXECUTE = 3;           // 1 << 0 | 1 << 1

    const MASK__EVENTS_VIEW = 4;                    // 1 << 2
    const MASK__EVENTS_CASHIER = 12;                // 1 << 2 | 1 << 3
    const MASK__EVENTS_MANAGE = 28;                 // 1 << 2 | 1 << 3 | 1 << 4

    const MASK__SETTINGS_MANAGE = 32;               // 1 << 5
    const MASK__ADMINS_MANAGE = 64;                 // 1 << 6


    const MASK_STUDENTS_MANAGE = 128;               // 1 << 7
    const MASK_SUBSCRIBERS_MANAGE = 256;            // 1 << 8
    const MASK_SETTINGS_MANAGE = 544;               // 1 << 5 | 1 << 9
    const MASK_ADMINS_MANAGE = 1088;                // 1 << 6 | 1 << 10
    const MASK_EVENTS_MANAGE = 2076;                // 1 << 2 | 1 << 3 | 1 << 4 | 1 << 11

    const MASK_ACCOUNTS_VIEW = 4096;                // 1 << 12
    const MASK_ACCOUNTS_MANAGE = 12288;             // 1 << 12 | 1 << 13

    const MASK_VENDORS_VIEW = 16384;                // 1 << 14
    const MASK_VENDORS_MANAGE = 49152;              // 1 << 14 | 1 << 15

    const MASK_TRANSACTIONS_VIEW = 65536;           // 1 << 16
    const MASK_TRANSACTIONS_DEPOSIT = 196608;       // 1 << 16 | 1 << 17
    const MASK_TRANSACTIONS_WITHDRAWAL = 458752;    // 1 << 16 | 1 << 17 | 1 << 18
    const MASK_TRANSACTIONS_VENDOR = 983040;        // 1 << 16 | 1 << 17 | 1 << 18 | 1 << 19
    const MASK_TRANSACTIONS_GOD = 2031619;          // 1 << 0 | 1 << 1 | 1 << 16 | 1 << 17 | 1 << 18 | 1 << 19 | 1 << 20



    /**
     * Returns the mask for the passed code.
     *
     * @param mixed $code
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function resolveMask($code)
    {
        if (is_string($code)) {
            if (!defined($name = sprintf('static::MASK_%s', strtoupper($code)))) {
                throw new \InvalidArgumentException(sprintf('The code "%s" is not supported', $code));
            }

            return constant($name);
        }

        if (!is_int($code)) {
            throw new \InvalidArgumentException('$code must be an integer.');
        }

        return $code;
    }

    public function isVendorSpecific()
    {
        return ($this->mask & ~self::VENDOR_SPECIFIC_ROLES) === 0;
    }

    public function isIncluded($mask)
    {
        return ($mask & $this->mask) === $this->mask;
    }
}