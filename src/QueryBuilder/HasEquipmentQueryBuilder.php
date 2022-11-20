<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\Nested;
use Elastica\Query\Term;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HasEquipmentQueryBuilder
{
    private ?string $equipmentsProperty;
    private ?string $equipmentsPrefix;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->equipmentsProperty = $parameterBag->get('app_equipment_property');
        $this->equipmentsPrefix = $parameterBag->get('app_equipment_property_prefix');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $optionQuery = new BoolQuery();

        foreach ($data as $value) {
            $termQuery = new Term();
            $termQuery->setTerm($this->equipmentsProperty, $value);
            $equipmentDomain = new Nested();
            $equipmentDomain->setQuery($termQuery)->setPath($this->equipmentsPrefix);
            $optionQuery->addShould($equipmentDomain);
        }

        return $optionQuery;
    }
}