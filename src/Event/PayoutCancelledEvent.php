<?php

namespace App\Event;

use App\Entity\Payout;
use Symfony\Contracts\EventDispatcher\Event;

class PayoutCancelledEvent extends Event
{
    public function  __construct(private Payout $payout)
    {
    }

    public function getPayout(): Payout
    {
        return $this->payout;
    }
}
