<?php

namespace App\Criteria;

use App\Entity\Promotion;
use Doctrine\ORM\QueryBuilder;

interface CriteriaInterface
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;

    public function verify(Promotion $promotion): bool;
}
