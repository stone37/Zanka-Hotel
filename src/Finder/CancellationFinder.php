<?php

namespace App\Finder;

use App\QueryBuilder\GenericQueryBuilder;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Finder\FinderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CancellationFinder
{
    private GenericQueryBuilder $genericQueryBuilder;
    private ?string $idProperty;

    public function __construct(
        GenericQueryBuilder $genericQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->genericQueryBuilder = $genericQueryBuilder;
        $this->idProperty = $parameterBag->get('app_id_property');
    }

    public function find(int $id): ?array
    {
        if (!$id) {return null;}

        $boolQuery = $this->genericQueryBuilder->buildQuery();

        $idQuery = new Term();
        $idQuery->setTerm($this->idProperty, $id);
        $boolQuery->addMust($idQuery);

        return $this->cancellationFinder->findHybrid($boolQuery);
    }
}
