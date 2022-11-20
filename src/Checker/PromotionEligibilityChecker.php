<?php

namespace App\Checker;

use App\Criteria\PromotionCriteria;
use App\Entity\Promotion;

class PromotionEligibilityChecker
{
    private PromotionCriteria $criteria;

    public function __construct(PromotionCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function isPromotionEligible(Promotion $promotion): bool
    {
        foreach ($this->criteria as $criterion) {
            if (!$criterion->verify($promotion)) {
                return false;
            }
        }

        return true;
    }
}

