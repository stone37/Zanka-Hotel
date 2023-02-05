<?php

namespace App\Context;

use App\Repository\RoomEquipmentRepository;

class RoomEquipmentContext
{
    public function __construct(private RoomEquipmentRepository $repository)
    {
    }

    public function getEquipments(): ?array
    {
        return $this->repository->getPartial(20);
    }
}
