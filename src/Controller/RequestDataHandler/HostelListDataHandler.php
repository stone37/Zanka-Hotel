<?php

namespace App\Controller\RequestDataHandler;

use App\Exception\CityNotFoundException;
use App\Repository\CityRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HostelListDataHandler
{
    private CityRepository $cityRepository;
    private ?string $namePropertyPrefix;
    private ?string $cityPropertyPrefix;
    private ?string $countryPropertyPrefix;
    private ?string $averageRatingPropertyPrefix;
    private ?string $occupantPropertyPrefix;

    public function __construct(
        CityRepository $cityRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->cityRepository = $cityRepository;
        $this->namePropertyPrefix = $parameterBag->get('app_name_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
        $this->countryPropertyPrefix = $parameterBag->get('app_country_property_prefix');
        $this->averageRatingPropertyPrefix = $parameterBag->get('app_average_rating_property_prefix');
        $this->occupantPropertyPrefix = $parameterBag->get('app_occupant_property_prefix');
    }

    public function retrieveData(array $requestData): array
    {
        $location = $requestData['location'];
        $city = $this->cityRepository->getByName($location);

        if (null === $city) {
            throw new CityNotFoundException();
        }

        $data[$this->namePropertyPrefix] = (string) $requestData[$this->namePropertyPrefix];
        $data[$this->cityPropertyPrefix] = strtolower($city->getName());
        $data[$this->countryPropertyPrefix] = $city->getCountry();
        $data[$this->occupantPropertyPrefix] = $requestData['adult'] + $requestData['children'] ;
        $data = array_merge($data, $requestData['price']);

        $this->handleEquipmentsPrefixedProperty($requestData, $data);
        $this->handleRoomEquipmentsPrefixedProperty($requestData, $data);
        $this->handleCategoryPrefixedProperty($requestData, $data);
        $this->handleStarNumberPrefixedProperty($requestData, $data);
        $data[$this->averageRatingPropertyPrefix] = (string) $requestData[$this->averageRatingPropertyPrefix];

        return $data;
    }

    private function handleEquipmentsPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['equipments'])) {
            return;
        }

        if (array_key_exists('equipments', $requestData['equipments'])) {
            $data['equipments'] = $requestData['equipments']['equipments'];
        } else {
            $data['equipments'] = $requestData['equipments'];
        }
    }

    private function handleRoomEquipmentsPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['roomEquipments'])) {
            return;
        }

        if (array_key_exists('roomEquipments', $requestData['roomEquipments'])) {
            $data['roomEquipments'] = $requestData['roomEquipments']['roomEquipments'];
        } else {
            $data['roomEquipments'] = $requestData['roomEquipments'];
        }
    }

    private function handleCategoryPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['category'])) {
            return;
        }

        $data['category'] = $requestData['category'];
    }

    private function handleStarNumberPrefixedProperty(array $requestData, array &$data)
    {
        if (!isset($requestData['starNumber'])) {
            return;
        }

        $data['starNumber'] = $requestData['starNumber'];
    }
}
