<?php


namespace DTO\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Class ArrayOfObjects
 * @package DTO\Validator
 */
class ArrayOfObjectsValidator extends ConstraintValidator
{
    public $message = 'Invalid value.';

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof ArrayOfObjects)){
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ArrayOfObjects');
        }
        $sType = (string) $constraint->type;
        if (!class_exists($sType)){
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ArrayOfObjects');
        }
        if (null !== $value && is_array($value)) {
            foreach ($value as $oObject){
                if (!($oObject instanceof $sType)){
                    throw new UnexpectedValueException($oObject, $sType);
                }
            }
            return;
        }
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}