<?php

namespace App\Calculator;

use App\Entity\Room;

class RoomPriceCalculator
{
    public function __construct(private TaxCalculator $taxCalculator)
    {
    }

    public function calculate(Room $room): int
    {
        $taxe = $this->getTaxe($room);

        return $room->getPrice() + $taxe;
    }

    public function calculateOrigin(Room $room): int
    {
        $taxe = $this->getTaxe($room);

        return $room->getOriginalPrice()+ $taxe;
    }

    public function getTaxe(Room $room): int
    {
        $value = 0;

        foreach ($room->getTaxes() as $taxe) {
            $value += $this->taxCalculator->calculate($room->getPrice(), $taxe);
        }

        return $value;
    }
}
