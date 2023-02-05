<?php

namespace App\Event;

use App\Data\BookingData;
use App\Entity\Commande;

class PaymentEvent
{
    private BookingData $data;
    private Commande $commande;

    public function __construct(BookingData $data, Commande $commande)
    {
        $this->data = $data;
        $this->commande = $commande;
    }

    public function getData(): BookingData
    {
        return $this->data;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}

