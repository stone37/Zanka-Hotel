<?php

namespace App\Calculator;

use DateTimeInterface;

class DaysCalculator
{
    public function getDays(DateTimeInterface $start, DateTimeInterface $end): int
    {
        $interval = date_diff($start, $end);

        return (int) $interval->format('%a');
    }
}
