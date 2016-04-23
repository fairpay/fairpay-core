<?php


namespace Fairpay\Bundle\UserBundle\Security\Acl;



class MaskBuilder extends AbstractMaskBuilder
{
    const MASK_TRANSACTION_VIEW = 1;        // 1 << 0
    const MASK_TRANSACTION_EXECUTE = 3;     // 1 << 0 | 1 << 1

    const MASK_EVENT_VIEW = 4;              // 1 << 2
    const MASK_EVENT_CASHIER = 12;          // 1 << 2 | 1 << 3
    const MASK_EVENT_MANAGE = 28;           // 1 << 2 | 1 << 3 | 1 << 4

    const MASK_SETTINGS_MANAGE = 32;        // 1 << 5
    const MASK_ADMIN_MANAGE = 64;           // 1 << 6

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
}