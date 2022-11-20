<?php

namespace App\Calculator;

use App\Entity\PromotionAction;
use App\Entity\Room;

class FixedDiscountPriceCalculator
{
    public const TYPE = 'fixed_discount';

    public function supports(PromotionAction $action): bool
    {
        return $action->getType() === self::TYPE;
    }

    public function calculate(Room $room, PromotionAction $action): int
    {
        $price = $room->getPrice() - $action->getConfiguration()['amount'];

        return $price;
    }
}