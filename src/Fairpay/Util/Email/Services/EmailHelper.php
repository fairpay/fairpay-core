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

    public function isDisposable($email)
    {
        return in_array($this->getDomain($email), $this->disposableDomains);
    }

    public function isStandard($email)
    {
        return in_array($this->getMainDomain($email), $this->standardDomains);
    }

    public function getDomain($email)
    {
        try {
            return explode('@', $email)[1];
        } catch(ContextErrorException $e) {
            return null;
        }
    }

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