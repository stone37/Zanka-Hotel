<?php

namespace App\Event;

use App\Entity\BookingSearchToken;

final class BookingSearchTokenCreatedEvent
{
    public function __construct(private BookingSearchToken $token)
    {
    }

    public function getToken(): BookingSearchToken
    {
        return $this->token;
    }
}
