<?php

namespace App\Finder;

use App\QueryBuilder\GenericQueryBuilder;
use Elastica\Query;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CitiesFinder
{
    private GenericQueryBuilder $genericQueryBuilder;
    private FinderInterface $citiesFinder;
    private ?string $positionPrefix;
    private ?string $enabledProperty;

    public function __construct(
        FinderInterface $citiesFinder,
        GenericQueryBuilder $genericQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->citiesFinder = $citiesFinder;
        $this->genericQueryBuilder = $genericQueryBuilder;
        $this->positionPrefix = $parameterBag->get('app_position_property_prefix');
        $this->enabledProperty = $parameterBag->get('app_enabled_property');
    }

    public function find(int $limit = null): ?array
    {
        $boolQuery = $this->genericQueryBuilder->buildQuery();

        $enabledQuery = new Term();
        $enabledQuery->setTerm($this->enabledProperty, false);
        $boolQuery->addMust($enabledQuery);

        $query = new Query($boolQuery);
        $query->addSort([$this->positionPrefix => ['order' => 'asc']]);

        return $this->citiesFinder->find($query, $limit);
    }
}
