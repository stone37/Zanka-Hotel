<?php

namespace App\Event;

use App\Entity\Hostel;

class HostelConfirmedEvent
{
    public function __construct(private Hostel $hostel)
    {
    }

    public function getHostel(): Hostel
    {
        return $this->hostel;
    }
}
