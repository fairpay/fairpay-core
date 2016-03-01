<?php


namespace Fairpay\Util\Email\Services;


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
     * Checks if domain is valid.
     *
     * @param string $email
     * @return bool
     */
    public function isValidDomain($email)
    {
        return 1 === preg_match('/^([a-z](-?[a-z0-9]+)+-?\.)+[a-z]{2,4}$/', $this->getDomain($email));
    }

    /**
     * Get an email domain. Return what comes after the @ sign, or the whole string if no @ sign is found.
     *
     * @param string $email
     * @return string
     */
    public function getDomain($email)
    {
        $email = $this->getEmail($email);

        $parts = explode('@', $email);
        return isset($parts[1]) ? $parts[1] : $email;
    }

    /**
     * Get an email main domain (first domain before the first dot).
     *
     * @param string $email
     * @return string|null
     */
    public function getMainDomain($email)
    {
        $domain = $this->getDomain($email);
        return substr($domain, 0, strpos($domain, '.'));
    }

    /**
     * If $email is an object returns the result of $email->getEmail(), return $email otherwise.
     *
     * @param object|string $email
     * @return string
     */
    public function getEmail($email)
    {
        if (is_string($email)) {
            return $email;
        }

        if (is_object($email)) {
            if (method_exists($email, 'getEmail')) {
                return $email->getEmail();
            }

            throw new \InvalidArgumentException('Object does not have a method getEmail.');
        }

        throw new \InvalidArgumentException('Argument must be a string or an object.');
    }
}