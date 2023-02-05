<?php

namespace App\Checker;

use App\Criteria\PromotionCriteria;
use App\Entity\Promotion;
use DateTime;

class PromotionEligibilityChecker
{
    private PromotionCriteria $criteria;

    public function __construct(PromotionCriteria $criteria)
    {
        $this->criteria = $criteria;
    }

    public function isPromotionEligible(Promotion $promotion, DateTime $start, DateTime $end): bool
    {
        if (!$this->criteria->verify($promotion, $start, $end)) {
            return false;
        }

        return true;
    }

    /*public function isPromotionEligible(Promotion $promotion): bool
    {
        foreach ($this->criteria as $criterion) {
            if (!$criterion->verify($promotion)) {
                return false;
            }
        }

        return true;
    }*/
}

