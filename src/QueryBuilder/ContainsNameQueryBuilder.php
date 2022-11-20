<?php

namespace App\QueryBuilder;

use App\PropertyNameResolver\NameResolver;
use Elastica\Query\AbstractQuery;
use Elastica\Query\MatchQuery;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContainsNameQueryBuilder
{
    private NameResolver $hostelNameResolver;
    private ?string $namePropertyPrefix;

    public function __construct(
        NameResolver $hostelNameResolver,
        ParameterBagInterface $parameterBag
    )
    {
        $this->hostelNameResolver = $hostelNameResolver;
        $this->namePropertyPrefix = $parameterBag->get('app_name_property_prefix');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $propertyName = $this->hostelNameResolver->resolvePropertyName();

        if (!$name = $data[$this->namePropertyPrefix]) {
            return null;
        }

        $nameQuery = new MatchQuery();
        $nameQuery->setFieldQuery($propertyName, $name);
        $nameQuery->setFieldFuzziness($propertyName, 2);
        $nameQuery->setFieldMinimumShouldMatch($propertyName, 2);

        return $nameQuery;
    }
}