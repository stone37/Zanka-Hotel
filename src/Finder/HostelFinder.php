<?php

namespace App\Finder;

use App\Controller\RequestDataHandler\HostelSortDataHandler;
use App\Controller\RequestDataHandler\PaginationDataHandler;
use App\QueryBuilder\HostelsQueryBuilder;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Pagerfanta\Pagerfanta;

class HostelFinder
{
    private HostelsQueryBuilder $hostelQueryBuilder;
    private PaginatedFinderInterface $hostelFinder;

    public function __construct(
        HostelsQueryBuilder $hostelQueryBuilder,

    ) {
        $this->hostelQueryBuilder = $hostelQueryBuilder;
    }

    public function find(array $data): Pagerfanta
    {
        $boolQuery = $this->hostelQueryBuilder->buildQuery($data);

        $query = new Query($boolQuery);
        $query->addSort($data[HostelSortDataHandler::SORT_INDEX]);

        $hostels = $this->hostelFinder->findPaginated($query);
        $hostels->setMaxPerPage($data[PaginationDataHandler::LIMIT_INDEX]);
        $hostels->setCurrentPage($data[PaginationDataHandler::PAGE_INDEX]);

        return $hostels;
    }
}