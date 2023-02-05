<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\Term;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IsNotClosedQueryBuilder
{
    private ?string $closedProperty;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->closedProperty = $parameterBag->get('app_enabled_property');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $closedQuery = new Term();
        $closedQuery->setTerm($this->closedProperty, true);

        return $closedQuery;
    }
}