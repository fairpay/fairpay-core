<?php


namespace Fairpay\Util\Util;

/**
 * Generate a random string.
 */
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
}