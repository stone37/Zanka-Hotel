<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

class MoneyTransformer extends MoneyToLocalizedStringTransformer
{
    public function reverseTransform($value): ?int
    {
        /** @var int|float|null $value */
        $value = parent::reverseTransform($value);

        return null === $value ? null : (int) round($value);
    }
}
