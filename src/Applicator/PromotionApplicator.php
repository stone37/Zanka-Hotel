<?php

namespace App\Applicator;

use App\Checker\PromotionEligibilityChecker;
use App\Entity\Promotion;
use App\Entity\PromotionAction;
use App\Entity\Room;
use DateTime;

class PromotionApplicator
{
    private ActionBasedDiscountApplicator $actionBasedDiscountApplicator;
    private PromotionEligibilityChecker $promotionEligibilityChecker;

    public function __construct(
        ActionBasedDiscountApplicator $actionBasedDiscountApplicator,
        PromotionEligibilityChecker $promotionEligibilityChecker
    )
    {
        $this->actionBasedDiscountApplicator = $actionBasedDiscountApplicator;
        $this->promotionEligibilityChecker = $promotionEligibilityChecker;
    }

    public function applyOnRoom(Room $room, Promotion $promotion, DateTime $start, DateTime $end): Room
    {
        if (!$this->promotionEligibilityChecker->isPromotionEligible($promotion, $start, $end)) {
            return $room;
        }

        return $this->applyDiscountFromAction($promotion->getAction(), $room);
    }

    private function applyDiscountFromAction(PromotionAction $action, Room $room): Room
    {
        return $this->actionBasedDiscountApplicator->applyDiscount($action, $room);
    }

    /*public function applyOnRoom(Room $room, Promotion $promotion): void
    {
        if (!$this->promotionEligibilityChecker->isPromotionEligible($promotion)) {
            return;
        }

        $this->applyDiscountFromAction($promotion, $promotion->getAction(), $room);
    }

    private function applyDiscountFromAction(Promotion $promotion, PromotionAction $action, Room $room): void
    {
        $this->actionBasedDiscountApplicator->applyDiscount($promotion, $action, $room);
    }*/
}
