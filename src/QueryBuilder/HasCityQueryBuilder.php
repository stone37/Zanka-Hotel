<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\Nested;
use Elastica\Query\Terms;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HasCityQueryBuilder
{
    private ?string $citiesProperty;
   // private ?string $citiesNameProperty;
    private ?string $prefix;
    private ?string $locationProperty;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->citiesProperty = $parameterBag->get('app_city_property');
        //$this->citiesNameProperty = $parameterBag->get('app_city_name_property');
        $this->prefix = $parameterBag->get('app_city_property_prefix');
        $this->locationProperty = $parameterBag->get('app_location_property_prefix');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        if (!$cityName = $data[$this->prefix]) {
            return null;
        }

        /*$cityQuery = new Terms($this->citiesNameProperty);
        $cityQuery->setTerms([$cityName]);

        $cityDomainQuery = new Nested();
        $cityDomainQuery->setQuery($cityQuery)->setPath($this->citiesProperty);

        $locationDomainQuery = new Nested();
        $locationDomainQuery->setQuery($cityDomainQuery)->setPath($this->locationProperty);*/

        $cityQuery = new Terms($this->citiesProperty);
        $cityQuery->setTerms([$cityName]);

        $locationDomainQuery = new Nested();
        $locationDomainQuery->setQuery($cityQuery)->setPath($this->locationProperty);

        return $locationDomainQuery;
    }
}