<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

class IntegerToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $value)
    {
        if (empty($value)) {
            return null;
        }

        Assert::string($value);

        return (int) $value;
    }

    public function reverseTransform(mixed $value)
    {
        if (empty($value)) {
            return null;
        }

        return (string) $value;
    }
}
