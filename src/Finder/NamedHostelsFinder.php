<?php

namespace App\Finder;

use App\QueryBuilder\HostelByPartialNameQueryBuilder;
use FOS\ElasticaBundle\Finder\FinderInterface;

class NamedHostelsFinder
{
    private HostelByPartialNameQueryBuilder $hostelsByPartialNameQueryBuilder;
    private FinderInterface $hostelsFinder;

    public function __construct(
        HostelByPartialNameQueryBuilder $hostelsByPartialNameQueryBuilder,
        FinderInterface $hostelsFinder
    ) {
        $this->hostelsByPartialNameQueryBuilder = $hostelsByPartialNameQueryBuilder;
        $this->hostelsFinder = $hostelsFinder;
    }

    public function findByNamePart(string $namePart): ?array
    {
        $data = ['name' => $namePart];
        $query = $this->hostelsByPartialNameQueryBuilder->buildQuery($data);

        return $this->hostelsFinder->find($query);
    }
}
