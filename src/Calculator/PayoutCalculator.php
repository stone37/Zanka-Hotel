<?php

namespace App\Calculator;

use App\Entity\Plan;

class PayoutCalculator
{
    public function __construct(private CommissionCalculator $calculator)
    {
    }

    public function calculate(Plan $plan, int $amountTotal, int $amountCommission): int
    {
        return $amountTotal - $this->calculator->calculate($plan, $amountCommission);
    }
}