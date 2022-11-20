<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Webmozart\Assert\Assert;

class DateStringToArrayTransformer implements DataTransformerInterface
{

    public function transform(mixed $value)
    {
        if (null === $value) {
            return '';
        }

        Assert::isArray($value);

        return $value['checkin'] . ' - ' . $value['checkout'];
    }

    public function reverseTransform(mixed $value)
    {
        if (!$value) {
            return null;
        }

        $result = explode(' - ', $value);

        if (null === $result || count($result) === 0 || count($result) > 2) {
            throw new TransformationFailedException('Données non prise en charge');
        }

        return ['checkin' => trim($result[0]), 'checkout' => trim($result[1])];
    }
}
