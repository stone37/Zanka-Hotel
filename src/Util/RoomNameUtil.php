<?php

namespace App\Util;

use App\Entity\Room;

class RoomNameUtil
{
    public function getName(Room $room): string
    {
        $name = $room->getType();

        if ($room->getSpecification()) {
            $name .= ' '.$room->getSpecification();
        }

        if ($room->getFeature()) {
            $name .= ' - '.$room->getFeature();
        }

        if ($room->getAmenities()) {
            $name .= ' '. strtolower($room->getAmenities());
        }

        return $name;
    }
}