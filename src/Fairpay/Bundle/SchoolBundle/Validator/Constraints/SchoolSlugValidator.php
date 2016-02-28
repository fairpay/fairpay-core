<?php


namespace Fairpay\Bundle\SchoolBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchoolSlugValidator extends ConstraintValidator
{
    /**
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SchoolSlug) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\SchoolSlug');
        }

        if (!preg_match('/^[a-z](-?[a-z0-9]+)+$/', $value) || in_array($value, ['api', 'www'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}