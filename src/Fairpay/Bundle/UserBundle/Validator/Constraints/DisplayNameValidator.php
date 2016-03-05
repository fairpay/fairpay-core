<?php


namespace Fairpay\Bundle\UserBundle\Validator\Constraints;

use Fairpay\Util\Util\StringUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class DisplayNameValidator extends ConstraintValidator
{
    /** @var  StringUtil */
    private $stringUtil;

    /**
     * DisplayNameValidator constructor.
     * @param StringUtil $stringUtil
     */
    public function __construct(StringUtil $stringUtil)
    {
        $this->stringUtil = $stringUtil;
    }

    /**
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DisplayName) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\DisplayName');
        }

        $urlized = $this->stringUtil->urlize($value, '.');

        if (strlen($urlized) < 2) {
            if (strlen($value) >= 2) {
                $this->context->buildViolation('Vous devez utiliser au moins 2 caractères non-spéciaux.')
                    ->addViolation();
            } else {
                $this->context->buildViolation('Votre nom d\'utilisateur doit faire au moins 2 caractères de long.')
                    ->addViolation();
            }
        }
    }
}