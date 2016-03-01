<?php


namespace Fairpay\Util\Email\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Make sure the email address is not from a standard email provider.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class ValidDomain extends Constraint
{
    public $message = 'Ce nom de domaine n\'est pas valide.';

    public function getDefaultOption()
    {
        return 'message';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'fairpay.valid_domain';
    }
}