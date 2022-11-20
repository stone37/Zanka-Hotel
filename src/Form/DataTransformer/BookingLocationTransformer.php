<?php

namespace App\Form\DataTransformer;

use App\Repository\CityRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Intl\Countries;

class BookingLocationTransformer implements DataTransformerInterface
{
    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    public function transform(mixed $value)
    {
        if (null === $value) {
            return '';
        }

        $city = $this->cityRepository->getByName($value);

        if ($city === null) {
            return '';
        }

        return $value . ', ' . Countries::getName(strtoupper($city->getCountry()));
    }

    public function reverseTransform(mixed $value)
    {
        if (!$value) {
            return 'all';
        }

        $result = explode(', ', $value);

        if (null === $result) {
            throw new TransformationFailedException('Données non prise en charge');
        }

        return $result[0];
    }
}
