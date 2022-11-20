<?php

namespace App\Calculator;

use DateTime;

class DaysCalculator
{
    public function getDays(DateTime $start, DateTime $end): int
    {
        $interval = date_diff($start, $end);

        return (int) $interval->format('%a');
    }
}
