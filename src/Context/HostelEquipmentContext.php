<?php

namespace App\Context;

use App\Repository\EquipmentRepository;

class HostelEquipmentContext
{
    private EquipmentRepository $repository;

    public function __construct(EquipmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getEquipments(): array
    {
        return $this->repository->getPartial(20);
    }
}
