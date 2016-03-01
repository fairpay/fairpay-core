<?php


namespace Fairpay\Util\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToStringTransformer implements DataTransformerInterface
{
    public function transform($array)
    {
        if (null === $array) {
            $array = array();
        }

        if (!is_array($array)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return implode(', ', $array);
    }

    public function reverseTransform($value)
    {
        $value = preg_replace('/\s*,\s*/', ',', trim($value));

        if ($value === '') {
            return array();
        }

        return explode(',', $value);
    }
}