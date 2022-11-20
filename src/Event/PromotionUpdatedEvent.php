<?php

namespace App\Event;

use App\Entity\Promotion;

class PromotionUpdatedEvent
{
    private Promotion $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    public function getPromotion(): Promotion
    {
        return $this->promotion;
    }
}

