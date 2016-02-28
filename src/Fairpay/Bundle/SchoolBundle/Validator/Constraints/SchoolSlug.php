<?php


namespace Fairpay\Bundle\SchoolBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
class SchoolSlug extends Constraint
{
    public $message = 'Cette url n\'est pas valide.';

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
}