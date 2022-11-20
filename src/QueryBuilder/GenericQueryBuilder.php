<?php

namespace App\QueryBuilder;

use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;

class GenericQueryBuilder
{
    public function buildQuery(): ?AbstractQuery
    {
        $boolQuery = new BoolQuery();

        return $boolQuery;
    }
}