<?php

namespace App\Manager;

use App\Repository\BannerRepository;
use App\Storage\SessionStorage;


class BannerManager
{
    private BannerRepository $repository;
    private SessionStorage $storage;

    public function __construct(BannerRepository $repository, SessionStorage $storage)
    {
        $this->repository = $repository;
        $this->storage = $storage;
    }

    public function get(string $type, string $location)
    {
        return $this->repository->getEnabled($type, $location);
    }

    public function add(int $id)
    {
        $bannerId = $this->storage->get($this->provideKey());

        $bannerId[] = $id;

        $this->storage->set($this->provideKey(), $bannerId);
    }

    public function has(int $id): bool
    {
        if (!$this->storage->has($this->provideKey())) {
            $this->storage->set($this->provideKey(), []);
        }

        $bannerId = $this->storage->get($this->provideKey());

        return in_array($id, $bannerId);
    }

    private function provideKey(): string
    {
        return '_app_banner';
    }
}