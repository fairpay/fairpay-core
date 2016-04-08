<?php


namespace Fairpay\Util\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Fairpay\Bundle\SchoolBundle\Manager\SchoolManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueEntityValidator extends ConstraintValidator
{
    /** @var EntityManager */
    private $em;

    /** @var  SchoolManager */
    private $schoolManager;

    /**
     * UniqueEntityValidator constructor.
     * @param EntityManager $em
     * @param SchoolManager $schoolManager
     */
    public function __construct(EntityManager $em, SchoolManager $schoolManager)
    {
        $this->em = $em;
        $this->schoolManager = $schoolManager;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $object     The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\UniqueEntity');
        }

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        $criteria = array();
        foreach ($fields as $fieldName) {
            $criteria[$fieldName] = $object->$fieldName;

            if ($constraint->ignoreNull && null === $criteria[$fieldName]) {
                return;
            }
        }

        if ($school = $this->schoolManager->getCurrentSchool()) {
            $criteria['school'] = $school;
        }

        $repo = $this->em->getRepository($constraint->entity);
        $result = $repo->findBy($criteria);

        if (0 === count($result) || (1 === count($result) && isset($object->id) && $object->id == $result[0]->getId())) {
            return;
        }

        $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $fields[0];
        $invalidValue = isset($criteria[$errorPath]) ? $criteria[$errorPath] : $criteria[$fields[0]];

        $this->context->buildViolation($constraint->message)
            ->atPath($errorPath)
            ->setInvalidValue($invalidValue)
            ->addViolation();
    }
}