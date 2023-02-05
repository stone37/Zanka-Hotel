<?php

namespace App\Event;

use App\Entity\User;

class PartnerConfirmedEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
