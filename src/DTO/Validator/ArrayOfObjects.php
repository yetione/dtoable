<?php


namespace DTO\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class ArrayOfObjects
 * @package DTO\Validator
 * @Annotation
 */
class ArrayOfObjects extends Constraint
{
    public $message = 'Invalid value.';

    public $type;

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'type';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['type'];
    }
}