<?php

namespace App\Criteria;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Promotion;

final class Enabled implements CriteriaInterface
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($root.'.enabled = :enabled')
            ->setParameter('enabled', true);

        return $queryBuilder;
    }

    public function verify(Promotion $promotion): bool
    {
        return $promotion->isEnabled();
    }
}
