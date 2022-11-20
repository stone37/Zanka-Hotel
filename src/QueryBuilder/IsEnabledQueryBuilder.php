<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\Term;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class IsEnabledQueryBuilder
{
    private ?string $enabledProperty;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->enabledProperty = $parameterBag->get('app_enabled_property');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $enabledQuery = new Term();
        $enabledQuery->setTerm($this->enabledProperty, true);

        return $enabledQuery;
    }
}