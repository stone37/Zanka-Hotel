<?php

namespace App\Context;

use App\Finder\HostelEquipmentsFinder;

class HostelEquipmentContext
{
    private HostelEquipmentsFinder $finder;

    public function __construct(HostelEquipmentsFinder $finder)
    {
        $this->finder = $finder;
    }

    public function getEquipments(): ?array
    {
        return $this->finder->find(20);
    }
}