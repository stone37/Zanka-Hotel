<?php

namespace App\Event;

use App\Entity\Hostel;

class HostelViewEvent
{
    private Hostel $hostel;

    public function  __construct(Hostel $hostel)
    {
        $this->hostel = $hostel;
    }

    public function getHostel(): Hostel
    {
        return $this->hostel;
    }
}