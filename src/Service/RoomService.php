<?php

namespace App\Service;

use App\Calculator\PromotionPriceCalculator;
use App\Calculator\TaxCalculator;
use App\Checker\PromotionEligibilityChecker;
use App\Entity\Promotion;
use App\Entity\PromotionAction;
use App\Entity\Room;
use App\Repository\RoomRepository;
use App\Repository\BookingRepository;
use App\Storage\CartStorage;
use DateTime;

class RoomService
{
   public function __construct(
       private CartStorage $cartStorage,
       private RoomRepository $repository,
       private BookingRepository $bookingRepository,
       private PromotionEligibilityChecker $promotionEligibilityChecker,
       private PromotionPriceCalculator $promotionPriceCalculator,
       private TaxCalculator $taxCalculator
   )
   {
   }

    public function getRoom(): ?Room
    {
        $cart = $this->cartStorage->get();

        return $this->repository->getEnabled($cart['_room_id']);
    }

   public function hasPromotion(Room $room, DateTime $start, DateTime $end): bool
   {
       $response = false;

       foreach ($room->getPromotions() as $promotion) {
           $response = $this->promotionEligibilityChecker->isPromotionEligible($promotion, $start, $end);
       }

       return $response;
   }

    public function getPromotion(Room $room, DateTime $start, DateTime $end): ?Promotion
    {
        $response = null;

        foreach ($room->getPromotions() as $promotion) {
            if ($this->promotionEligibilityChecker->isPromotionEligible($promotion, $start, $end)) {
                $response = $promotion;
            }
        }

        return $response;
    }

    public function getPrice(Room $room, DateTime $start, DateTime $end): int
    {
        $promotion = $this->getPromotion($room, $start, $end);

        if ($promotion) {
            return $this->promotionPriceCalculator->calculate($room, $promotion->getAction());
        }

        return $room->getPrice();
    }

    public function getTaxe(Room $room): int
    {
        $value = 0;

        foreach ($room->getTaxes() as $taxe) {
            $value += $this->taxCalculator->calculate($room->getPrice(), $taxe);
        }

        return $value;
    }

    public function getPromotionData(PromotionAction $action): string
    {
        $amount = $action->getConfiguration()['amount'];

        if ($action->getType() === 'percentage_discount') {
            return '-' . $amount * 100 . ' %';
        }

        return '-' . $amount;
    }

    public function availableForPeriod(Room $room, DateTime $start, DateTime $end):int
    {
        return $this->bookingRepository->availableForPeriod($room, $start, $end);
    }
}
