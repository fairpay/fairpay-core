<?php


namespace Fairpay\Util\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Just like the standard Symfony UniqueEntity constraint but with an extra entity field (should be the entity shortcut
 * name). If the object has a public $id field it will be used to check against any result from the DB.
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntity extends Constraint
{
    public $message = 'This value is already used.';
    public $fields = array();
    public $entity = null;
    public $errorPath = null;
    public $ignoreNull = true;

    public function getRequiredOptions()
    {
        return array('fields', 'entity');
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'fairpay.unique_entity';
    }
}