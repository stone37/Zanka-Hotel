<?php

namespace App\Util;

use App\Entity\Booking;
use DateTime;

class BookingUtil
{
    public function hasCancelled(Booking $booking): bool
    {
        $checkin = (new DateTime())->setTimestamp($booking->getCheckin()->getTimestamp());

        return $checkin >= new DateTime();
    }
}
