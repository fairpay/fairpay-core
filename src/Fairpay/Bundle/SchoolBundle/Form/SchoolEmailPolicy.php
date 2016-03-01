<?php


namespace Fairpay\Bundle\SchoolBundle\Form;

use Fairpay\Bundle\SchoolBundle\Entity\School;
use Fairpay\Util\Email\Validator\Constraints\NotDisposableEmail;
use Fairpay\Util\Email\Validator\Constraints\NotStandardEmail;
use Fairpay\Util\Email\Validator\Constraints\ValidDomain;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SchoolEmailPolicy
{
    public $allowUnregisteredEmails;

    /**
     * @Assert\All({
     *     @NotDisposableEmail(),
     *     @NotStandardEmail(),
     *     @ValidDomain()
     * })
     */
    public $allowedEmailDomains;

    public function __construct(School $school)
    {
        $this->allowUnregisteredEmails = $school->getAllowUnregisteredEmails();
        $this->allowedEmailDomains = $school->getAllowedEmailDomains();
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        dump(count($this->allowedEmailDomains));
        if (!count($this->allowedEmailDomains) && $this->allowUnregisteredEmails) {
            $context->buildViolation('Si vous autorisez l\'inscription avec une adresse de l\'école, vous devez indiquer le domaine.')
                ->atPath('allowedEmailDomains')
                ->addViolation();
        }
    }
}