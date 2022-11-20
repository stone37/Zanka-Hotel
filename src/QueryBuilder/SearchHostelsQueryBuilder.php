<?php

namespace App\QueryBuilder;

use App\PropertyNameResolver\SearchPropertyNameResolverRegistry;
use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\MultiMatch;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SearchHostelsQueryBuilder
{
    public const QUERY_KEY = 'query';

    private SearchPropertyNameResolverRegistry $searchPropertyNameResolverRegistry;
    private IsEnabledQueryBuilder $isEnabledQueryBuilder;
    private ?string $fuzziness;

    public function __construct(
        SearchPropertyNameResolverRegistry $searchPropertyNameResolverRegistry,
        IsEnabledQueryBuilder $isEnabledQueryBuilder,
        ParameterBagInterface $parameterBag
    ) {
        $this->searchPropertyNameResolverRegistry = $searchPropertyNameResolverRegistry;
        $this->isEnabledQueryBuilder = $isEnabledQueryBuilder;
        $this->fuzziness = $parameterBag->get('app_fuzziness');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        if (!array_key_exists(self::QUERY_KEY, $data)) {
            throw new RuntimeException(
                sprintf(
                    'Could not build search products query because there\'s no "query" key in provided data. ' .
                    'Got the following keys: %s',
                    implode(', ', array_keys($data))
                )
            );
        }

        $query = $data[self::QUERY_KEY];

        if (!is_string($query)) {
            throw new RuntimeException(
                sprintf(
                    'Could not build search products query because the provided "query" is expected to be a string ' .
                    'but "%s" is given.',
                    is_object($query) ? get_class($query) : gettype($query)
                )
            );
        }

        $multiMatch = new MultiMatch();
        $multiMatch->setQuery($query);
        $multiMatch->setFuzziness($this->fuzziness);

        $fields = [];

        foreach ($this->searchPropertyNameResolverRegistry->getPropertyNameResolvers() as $propertyNameResolver) {
            $fields[] = $propertyNameResolver;
        }

        $multiMatch->setFields($fields);
        $bool = new BoolQuery();
        $bool->addMust($multiMatch);
        $bool->addFilter($this->isEnabledQueryBuilder->buildQuery([]));

        return $bool;
    }
}