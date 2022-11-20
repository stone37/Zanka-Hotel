<?php

namespace App\Model\Admin;

class PayoutSearch
{
    private ?string $hostel = null;

    public function getHostel(): ?string
    {
        return $this->hostel;
    }

    public function setHostel(?string $hostel): void
    {
        $this->hostel = $hostel;
    }
}

