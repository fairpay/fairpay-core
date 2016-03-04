<?php


namespace Fairpay\Bundle\SchoolBundle\Validator\Constraints;

use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SchoolSlugValidator extends ConstraintValidator
{
    /** @var  SchoolManager */
    private $schoolManager;

    /**
     * SchoolSlugValidator constructor.
     * @param SchoolManager $schoolManager
     */
    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
    }

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

        if ('' != $value && !$this->schoolManager->isValidSlug($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}