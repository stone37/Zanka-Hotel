<?php

namespace App\Model\Admin;

use App\Entity\Hostel;

class PayoutSearch
{
    private ?Hostel $hostel = null;

    public function getHostel(): ?Hostel
    {
        return $this->hostel;
    }

    public function setHostel(?Hostel $hostel): void
    {
        $this->hostel = $hostel;
    }
}

