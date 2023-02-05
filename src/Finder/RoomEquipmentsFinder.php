<?php

namespace App\Finder;

use App\QueryBuilder\GenericQueryBuilder;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RoomEquipmentsFinder
{
    private GenericQueryBuilder $genericQueryBuilder;
    private FinderInterface $equipmentsFinder;
    private ?string $positionPrefix;

    public function __construct(
        GenericQueryBuilder $genericQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->genericQueryBuilder = $genericQueryBuilder;
        $this->positionPrefix = $parameterBag->get('app_position_property_prefix');
    }

    public function find(int $limit = null): ?array
    {
        $query = new Query($this->genericQueryBuilder->buildQuery());
        $query->addSort([$this->positionPrefix => ['order' => 'asc']]);

        return $this->equipmentsFinder->find($query, $limit);
    }
}