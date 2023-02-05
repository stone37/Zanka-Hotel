<?php

namespace App\Criteria;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Promotion;

class PromotionCriteria
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.startDate IS NULL OR %s.startDate <= :date', $root, $root))
            ->andWhere(sprintf('%s.endDate IS NULL OR %s.endDate > :date', $root, $root))
            ->andWhere($root.'.enabled = :enabled')
            ->setParameter('enabled', true)
            ->setParameter('date', new DateTime());

        return $queryBuilder;
    }

    public function verify(Promotion $promotion, DateTime $start, DateTime $end): bool
    {
        return
            ($promotion->getStartDate() === null || $promotion->getStartDate() <= $start) &&
            ($promotion->getEndDate() === null || $promotion->getEndDate() > $end) &&
            $promotion->isEnabled();
    }

    /*public function verify(Promotion $promotion): bool
    {
        return
            ($promotion->getStartDate() === null || $promotion->getStartDate() <=  new DateTime()) &&
            ($promotion->getEndDate() === null || $promotion->getEndDate() > new DateTime()) &&
            $promotion->isEnabled()
        ;
    }*/
}
