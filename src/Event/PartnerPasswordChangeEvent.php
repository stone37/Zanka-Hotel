<?php

namespace App\Event;

use App\Entity\User;

class PartnerPasswordChangeEvent
{
    public function __construct(private User $user, private string $password)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
