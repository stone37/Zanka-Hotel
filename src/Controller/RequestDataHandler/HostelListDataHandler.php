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


    public function __construct(
        CityRepository $cityRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->cityRepository = $cityRepository;
        $this->namePropertyPrefix = $parameterBag->get('app_name_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
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
        $data = array_merge($data, $requestData['price']);
        $this->handleEquipmentsPrefixedProperty($requestData, $data);

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
}
