<?php


namespace Fairpay\Util\Email\Validator\Constraints;

use Fairpay\Util\Email\Services\EmailHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotDisposableEmailValidator extends ConstraintValidator
{
    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * NotDisposableEmailValidator constructor.
     * @param EmailHelper $emailHelper
     */
    public function __construct(EmailHelper $emailHelper)
    {
        $this->emailHelper = $emailHelper;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotDisposableEmail) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueEntity');
        }

        if ($this->emailHelper->isDisposable($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}