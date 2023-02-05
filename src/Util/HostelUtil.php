<?php

namespace App\Util;

use App\Calculator\DaysCalculator;
use App\Entity\Cancelation;
use App\Entity\City;
use App\Entity\Equipment;
use App\Entity\Hostel;
use App\Entity\User;
use App\Repository\HostelRepository;
use DateTime;
use DateTimeInterface;

class HostelUtil
{
    private HostelRepository $hostelRepository;
    private DaysCalculator $daysCalculator;

    public function __construct(HostelRepository $hostelRepository, DaysCalculator $daysCalculator)
    {
        $this->hostelRepository = $hostelRepository;
        $this->daysCalculator = $daysCalculator;
    }

    public function cityHostelNumber(City $city): int
    {
        return $this->hostelRepository->getNumberByCity($city);
    }

    public function verify(User $user): bool
    {
        return !($this->hostelRepository->getNumberByUser($user) >= $user->getHostelNumber());
    }

    public function getEquipments(Hostel $hostel): array
    {
        $results = [];

        /** @var Equipment $equipment */
        foreach ($hostel->getEquipments() as $equipment) {
            if (!in_array($equipment->getEquipmentGroup()->getName(), $results)) {
                $results[$equipment->getEquipmentGroup()->getName()] = [];
            }
        }

        /** @var Equipment $equipment */
        foreach ($hostel->getEquipments() as $equipment) {
            array_push($results[$equipment->getEquipmentGroup()->getName()], $equipment->getName());
        }

        return $results;
    }

    public function cancellation(Cancelation $cancellation, DateTimeInterface $startDate): ?DateTime
    {
        $date = new DateTime();
        $days = ($this->daysCalculator->getDays($startDate, $date) - $cancellation->getState()) + 1;

        if ($days < 0) {
            return null;
        }

        return $date->modify('+ ' . $days . ' day');
    }
}

