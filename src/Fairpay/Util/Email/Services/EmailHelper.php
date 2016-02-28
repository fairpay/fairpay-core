<?php


namespace Fairpay\Util\Email\Services;


use Symfony\Component\Debug\Exception\ContextErrorException;

class EmailHelper
{
    private $disposableDomains;
    private $standardDomains;

    /**
     * EmailHelper constructor.
     * @param $disposableDomains
     * @param $standardDomains
     */
    public function __construct($disposableDomains, $standardDomains)
    {
        $this->disposableDomains = $disposableDomains;
        $this->standardDomains = $standardDomains;
    }

    /**
     * Checks whether or not an email is disposable based on a black list of domains (cf: app/config/email).
     *
     * @param string $email
     * @return bool
     */
    public function isDisposable($email)
    {
        return in_array($this->getDomain($email), $this->disposableDomains);
    }

    /**
     * Checks whether or not an email is from a known provider based on a white list of domains (cf: app/config/email).
     *
     * @param string $email
     * @return bool
     */
    public function isStandard($email)
    {
        return in_array($this->getMainDomain($email), $this->standardDomains);
    }

    /**
     * Get an email domain.
     *
     * @param string $email
     * @return string|null
     */
    public function getDomain($email)
    {
        try {
            return explode('@', $email)[1];
        } catch(ContextErrorException $e) {
            return null;
        }
    }

    /**
     * Get an email main domain (first domain after @ sign).
     *
     * @param string $email
     * @return string|null
     */
    public function getMainDomain($email)
    {
        try {
            preg_match('/@([^.]+)\./', $email, $matches);
            return $matches[1];
        } catch(ContextErrorException $e) {
            return null;
        }
    }
}