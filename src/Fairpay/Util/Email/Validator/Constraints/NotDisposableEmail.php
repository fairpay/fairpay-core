<?php


namespace Fairpay\Util\Email\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
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