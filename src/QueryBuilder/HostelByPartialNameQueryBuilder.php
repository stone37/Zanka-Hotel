<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;

class HostelByPartialNameQueryBuilder
{
    private ContainsNameQueryBuilder $containsNameQueryBuilder;

    public function __construct(ContainsNameQueryBuilder $containsNameQueryBuilder)
    {
        $this->containsNameQueryBuilder = $containsNameQueryBuilder;
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $boolQuery = new BoolQuery();

        $nameQuery = $this->containsNameQueryBuilder->buildQuery($data);

        if (null !== $nameQuery) {
            $boolQuery->addFilter($nameQuery);
        }

        return $boolQuery;
    }
}