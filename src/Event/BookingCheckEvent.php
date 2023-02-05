<?php

namespace App\Event;

use App\Data\BookingData;
use App\Entity\Commande;
use App\Entity\Room;

class BookingCheckEvent
{
    public function __construct(private Room $room)
    {
    }

    public function getRoom(): Room
    {
        return $this->room;
    }
}

