<?php

namespace App\Calculator;

use App\Entity\PromotionAction;
use App\Entity\Room;

class PercentageDiscountPriceCalculator
{
    public const TYPE = 'percentage_discount';

    public function supports(PromotionAction $action): bool
    {
        return $action->getType() === self::TYPE;
    }

    public function calculate(Room $room, PromotionAction $action): int
    {
        $price = (int)($room->getPrice() - ($room->getPrice() * $action->getConfiguration()['amount']));

        return $price;
    }
}