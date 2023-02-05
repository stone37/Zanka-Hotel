<?php

namespace App\Event;

use App\Entity\CancelPayout;
use Symfony\Contracts\EventDispatcher\Event;

class CancelPayoutCompletedEvent extends Event
{
    public function  __construct(private CancelPayout $payout)
    {
    }

    public function getPayout(): CancelPayout
    {
        return $this->payout;
    }
}
