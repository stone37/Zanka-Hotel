<?php

namespace App\Model\Admin;

class ReviewSearch
{
    private ?string $hostel = null;

    public function getHostel(): ?string
    {
        return $this->hostel;
    }

    public function setHostel(?string $hostel): self
    {
        $this->hostel = $hostel;

        return $this;
    }
}
