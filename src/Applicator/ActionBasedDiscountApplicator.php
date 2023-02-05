<?php

namespace App\Applicator;

use App\Calculator\PromotionPriceCalculator;
use App\Entity\PromotionAction;
use App\Entity\Room;
use App\Exception\ActionBasedPriceCalculatorNotFoundException;

class ActionBasedDiscountApplicator
{
    private PromotionPriceCalculator $priceCalculator;

    public function __construct(PromotionPriceCalculator $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
    }

    public function applyDiscount(PromotionAction $action, Room $room): Room
    {
        try {
            $price = $this->priceCalculator->calculate($room, $action);
        } catch (ActionBasedPriceCalculatorNotFoundException) {}

        if (null === $room->getOriginalPrice()) {
            $room->setOriginalPrice($room->getPrice());
        }

        $room->setPrice($price);

        return $room;
    }

    /*public function applyDiscount(Promotion $promotion, PromotionAction $action, Room $room): void
    {
        try {
            $price = $this->priceCalculator->calculate($room, $action);
        } catch (ActionBasedPriceCalculatorNotFoundException) {
            return;
        }

        if (null === $room->getOriginalPrice()) {
            $room->setOriginalPrice($room->getPrice());
        }

        $room->setPrice($price);
        $room->addPromotion($promotion);
    }*/
}

