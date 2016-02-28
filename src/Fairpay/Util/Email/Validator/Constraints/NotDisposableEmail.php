<?php


namespace Fairpay\Util\Email\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Make sure the email address is not disposable.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class NotDisposableEmail extends Constraint
{
    public $message = 'Utilisez une vrai adresse mail.';

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
        return 'fairpay.not_disposable_email';
    }
}