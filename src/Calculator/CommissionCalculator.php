<?php

namespace App\Calculator;

use App\Entity\Plan;

class CommissionCalculator
{
    public function calculate(Plan $plan, int $amount): int
    {
        return (int) (($amount * $plan->getPercent()) / 100);
    }
}
