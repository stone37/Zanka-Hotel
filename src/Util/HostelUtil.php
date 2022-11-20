<?php


namespace App\Util;


use App\Entity\City;
use App\Repository\HostelRepository;

class HostelUtil
{
    private HostelRepository $hostelRepository;

    public function __construct(HostelRepository $hostelRepository)
    {
        $this->hostelRepository = $hostelRepository;
    }

    public function cityHostelNumber(City $city): int
    {
        return $this->hostelRepository->getNumberByCity($city);
    }
}