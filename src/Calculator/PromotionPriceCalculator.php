<?php

namespace App\Calculator;

use App\Entity\PromotionAction;
use App\Entity\Room;
use App\Exception\ActionBasedPriceCalculatorNotFoundException;

class PromotionPriceCalculator
{
    private FixedDiscountPriceCalculator $fixedCalculator;
    private PercentageDiscountPriceCalculator $percentageCalculator;

    public function __construct(FixedDiscountPriceCalculator $fixedCalculator, PercentageDiscountPriceCalculator $percentageCalculator)
    {
        $this->fixedCalculator = $fixedCalculator;
        $this->percentageCalculator = $percentageCalculator;
    }

    public function calculate(Room $room, PromotionAction $action): int
    {
        if ($this->fixedCalculator->supports($action)) {
            return $this->fixedCalculator->calculate($room, $action);
        } elseif ($this->percentageCalculator->supports($action)) {
            return $this->percentageCalculator->calculate($room, $action);
        }

        throw new ActionBasedPriceCalculatorNotFoundException();
    }
}