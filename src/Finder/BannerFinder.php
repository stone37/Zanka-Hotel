<?php

namespace App\Finder;

use App\QueryBuilder\GenericQueryBuilder;
use Elastica\Query;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BannerFinder
{
    private GenericQueryBuilder $genericQueryBuilder;
    private FinderInterface $bannerFinder;
    private ?string $positionPrefix;
    private ?string $enabledProperty;

    public function __construct(
        GenericQueryBuilder $genericQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->genericQueryBuilder = $genericQueryBuilder;
        $this->positionPrefix = $parameterBag->get('app_position_property_prefix');
        $this->enabledProperty = $parameterBag->get('app_enabled_property');
    }

    public function find(int $limit = null): ?array
    {
        $boolQuery = $this->genericQueryBuilder->buildQuery();

        $enabledQuery = new Term();
        $enabledQuery->setTerm($this->enabledProperty, true);
        $boolQuery->addMust($enabledQuery);

        $query = new Query($boolQuery);
        $query->addSort([$this->positionPrefix => ['order' => 'asc']]);

        return $this->bannerFinder->find($query, $limit);
    }
}
