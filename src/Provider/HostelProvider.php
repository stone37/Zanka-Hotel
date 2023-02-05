<?php

namespace App\Provider;

use App\Repository\HostelRepository;
use FOS\ElasticaBundle\Provider\PagerInterface;
use FOS\ElasticaBundle\Provider\PagerProviderInterface;
use FOS\ElasticaBundle\Provider\PagerfantaPager;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

class HostelProvider implements PagerProviderInterface
{
    private HostelRepository $repository;

    public function __construct(HostelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function provide(array $options = []): PagerInterface
    {
        return new PagerfantaPager(new Pagerfanta(new ArrayAdapter($this->repository->findActive())));
    }
}
