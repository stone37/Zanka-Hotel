<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HostelsQueryBuilder
{
    private IsEnabledQueryBuilder $isEnabledQueryBuilder;
    private ContainsNameQueryBuilder $containsNameQueryBuilder;
    private HasCityQueryBuilder $hasCityQueryBuilder;
    private HasEquipmentQueryBuilder $hasEquipmentQueryBuilder;
    private HasPriceBetweenQueryBuilder $hasPriceBetweenQueryBuilder;
    private ?string $equipmentPrefix;

    public function __construct(
        IsEnabledQueryBuilder $isEnabledQueryBuilder,
        ContainsNameQueryBuilder $containsNameQueryBuilder,
        HasCityQueryBuilder $hasCityQueryBuilder,
        HasEquipmentQueryBuilder $hasEquipmentQueryBuilder,
        HasPriceBetweenQueryBuilder $hasPriceBetweenQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->isEnabledQueryBuilder = $isEnabledQueryBuilder;
        $this->containsNameQueryBuilder = $containsNameQueryBuilder;
        $this->hasCityQueryBuilder = $hasCityQueryBuilder;
        $this->hasEquipmentQueryBuilder = $hasEquipmentQueryBuilder;
        $this->hasPriceBetweenQueryBuilder = $hasPriceBetweenQueryBuilder;
        $this->equipmentPrefix = $parameterBag->get('app_equipment_property_prefix');
    }

    public function buildQuery(array $data): AbstractQuery
    {
        $boolQuery = new BoolQuery();

        $boolQuery->addMust($this->isEnabledQueryBuilder->buildQuery($data));

        $nameQuery = $this->containsNameQueryBuilder->buildQuery($data);
        $this->addMustIfNotNull($nameQuery, $boolQuery);

        $cityQuery = $this->hasCityQueryBuilder->buildQuery($data);
        $this->addMustIfNotNull($cityQuery, $boolQuery);

        $priceQuery = $this->hasPriceBetweenQueryBuilder->buildQuery($data);
        $this->addMustIfNotNull($priceQuery, $boolQuery);

        $this->resolveEquipmentQuery($boolQuery, $data);

        return $boolQuery;
    }

    private function resolveEquipmentQuery(BoolQuery $boolQuery, array $data): void
    {
        foreach ($data as $key => $value) {
            if (0 === strpos($key, $this->equipmentPrefix) && 0 < count($value)) {
                $optionQuery = $this->hasEquipmentQueryBuilder->buildQuery($value);
                $boolQuery->addMust($optionQuery);
            }
        }
    }

    private function addMustIfNotNull(?AbstractQuery $query, BoolQuery $boolQuery): void
    {
        if (null !== $query) {
            $boolQuery->addMust($query);
        }
    }
}