<?php

namespace App\Calculator;

use App\Entity\Taxe;

class TaxCalculator
{
    public function calculate(int $base, Taxe $taxe): float
    {
        if ($taxe->isIncludedInPrice()) {
            return round($base - ($base / (1 + $taxe->getValue())));
        }

        return round($base * $taxe->getValue()) / 100;
    }
}
