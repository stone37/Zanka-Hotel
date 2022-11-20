<?php

namespace App\Exception;

use Exception;

class ActionBasedPriceCalculatorNotFoundException extends Exception
{
    public function __construct(?Exception $previousException = null)
    {
        parent::__construct('Price calculator for given action type was not found.', 0, $previousException);
    }
}