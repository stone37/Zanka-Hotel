<?php

namespace App\Calculator;

use App\Entity\Room;

class RoomPriceCalculator
{
    public function calculate(Room $room): int
    {

        return $room->getPrice();
    }
}
