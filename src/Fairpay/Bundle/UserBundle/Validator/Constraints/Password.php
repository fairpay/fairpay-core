<?php


namespace Fairpay\Bundle\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Make sure property is a valid password.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class Password extends Constraint
{
    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}