<?php


namespace Fairpay\Bundle\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Make sure property is a valid school slug.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class DisplayName extends Constraint
{
    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'fairpay.display_name';
    }
}