<?php

namespace App\Util;

use App\Entity\City;
use App\Repository\CityRepository;

class CityUtil
{
    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function getCity(string $name): City
    {
        return $this->cityRepository->getByName($name);
    }
}
