<?php

namespace App\Util;

use App\Calculator\RoomPriceCalculator;
use App\Entity\Room;

class PriceUtil
{
    public RoomPriceCalculator $calculator;

    public function __construct(RoomPriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function getPrice(Room $room): int
    {
        return $this->calculator->calculate($room);
    }
}