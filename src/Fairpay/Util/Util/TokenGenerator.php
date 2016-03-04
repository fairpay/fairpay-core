<?php


namespace Fairpay\Util\Util;

/**
 * Generate a random string using the symfony random_bytes function.
 */
class TokenGenerator implements TokenGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function generateToken()
    {
        return rtrim(strtr(base64_encode(base64_encode(random_bytes(32))), '+/', '-_'), '=');
    }
}