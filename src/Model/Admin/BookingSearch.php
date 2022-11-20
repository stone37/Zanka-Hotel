<?php

namespace App\Model\Admin;

use App\Entity\Hostel;
use App\Entity\Room;

class BookingSearch
{
    private ?Hostel $hostel = null;

    private ?Room $room = null;

    private ?string $code = null;

    public function getHostel(): ?Hostel
    {
        return $this->hostel;
    }

    public function setHostel(?Hostel $hostel): self
    {
        $this->hostel = $hostel;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }
}

