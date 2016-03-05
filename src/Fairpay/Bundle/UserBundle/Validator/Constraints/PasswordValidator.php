<?php


namespace Fairpay\Bundle\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Password) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Password');
        }

        if (strlen($value) < 6) {
            $this->context->buildViolation('Le mot de passe doit faire au moins 6 caractères.')
                ->addViolation();
        }

        if (!preg_match('/[a-zA-Z]/', $value)) {
            $this->context->buildViolation('Le mot de passe doit comporter au moins une lettre.')
                ->addViolation();
        }

        if (!preg_match('/\d/', $value)) {
            $this->context->buildViolation('Le mot de passe doit comporter au moins un chiffre.')
                ->addViolation();
        }
    }
}