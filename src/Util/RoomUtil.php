<?php

namespace App\Util;

use App\Entity\Promotion;
use App\Entity\Room;
use App\Entity\RoomEquipment;
use App\Service\RoomService;
use App\Storage\BookingStorage;
use DateTime;

class RoomUtil
{
    public function __construct(
        private RoomService $service,
        private BookingStorage $storage,
        private StringToDateUtil $dateUtil
    )
    {
    }

    public function getTotalPrice(Room $room): int
    {
        return $this->service->getPrice($room, $this->getStartDate(), $this->getEndDate()) + $this->service->getTaxe($room);
    }

    public function getPrice(Room $room): int
    {
        return $this->service->getPrice($room, $this->getStartDate(), $this->getEndDate());
    }

    public function getTaxe(Room $room): int
    {
        return $this->service->getTaxe($room);
    }

    public function getPromotion(Room $room): ?Promotion
    {
        return $this->service->getPromotion($room, $this->getStartDate(), $this->getEndDate());
    }

    public function isPriceReduced(Room $room): bool
    {
        return $this->service->hasPromotion($room, $this->getStartDate(), $this->getEndDate());
    }

    public function getData(Promotion $promotion): string
    {
        return $this->service->getPromotionData($promotion->getAction());
    }

    public function getEquipments(Room $room)
    {
        $results = [];

        /** @var RoomEquipment $equipment */
        foreach ($room->getEquipments() as $equipment) {
            if (!in_array($equipment->getRoomEquipmentGroup()->getName(), $results)) {
                $results[$equipment->getRoomEquipmentGroup()->getName()] = [];
            }
        }

        /** @var  RoomEquipment $equipment */
        foreach ($room->getEquipments() as $equipment) {
            array_push($results[$equipment->getRoomEquipmentGroup()->getName()], $equipment->getName());
        }

        return $results;
    }

    public function getRoomNumber(Room $room)
    {
        return $this->service->availableForPeriod($room, $this->getStartDate(), $this->getEndDate());
    }

    private function getStartDate(): DateTime
    {
        return $this->dateUtil->converter($this->storage->getBookingData()->duration['checkin']);
    }

    private function getEndDate(): DateTime
    {
        return $this->dateUtil->converter($this->storage->getBookingData()->duration['checkout']);
    }
}

