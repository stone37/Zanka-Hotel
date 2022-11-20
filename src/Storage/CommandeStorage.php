<?php

namespace App\Storage;

use App\Entity\Commande;
use App\Repository\CommandeRepository;

class CommandeStorage
{
    private SessionStorage $storage;
    private CommandeRepository $repository;

    public function __construct(SessionStorage $storage, CommandeRepository $repository)
    {
        $this->storage = $storage;
        $this->repository = $repository;
    }

    public function getCommande(): ?Commande
    {
        if ($this->storage->has($this->provideKey())) {
            return $this->repository->find($this->get());
        }

        return null;
    }

    public function set(string $orderId): void
    {
        $this->storage->set($this->provideKey(), $orderId);
    }

    public function remove(): void
    {
        $this->storage->remove($this->provideKey());
    }

    public function get(): string
    {
        return $this->storage->get($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_order';
    }
}

