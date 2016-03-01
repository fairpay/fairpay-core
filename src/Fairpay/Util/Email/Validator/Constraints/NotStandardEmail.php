<?php


namespace Fairpay\Util\Email\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Make sure the email address is not from a standard email provider.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class NotStandardEmail extends Constraint
{
    public $message = 'Vous devez utiliser le nom de domaine de votre école.';

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
        return 'fairpay.not_standard_email';
    }
}