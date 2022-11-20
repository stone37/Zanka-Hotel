<?php

namespace App\Provider;

use App\Criteria\PromotionCriteria;
use App\Repository\PromotionRepository;

class EligiblePromotionsProvider
{
    private PromotionRepository $promotionRepository;
    private PromotionCriteria $criteria;

    public function __construct(PromotionRepository $promotionRepository, PromotionCriteria $criteria)
    {
        $this->promotionRepository = $promotionRepository;
        $this->criteria = $criteria;
    }

    public function provide(): array
    {
        return $this->promotionRepository->findByCriteria($this->criteria);
    }
}

