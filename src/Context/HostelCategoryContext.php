<?php

namespace App\Context;

use App\Repository\CategoryRepository;

class HostelCategoryContext
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    public function getCategories(): ?array
    {
        return $this->repository->getEnabled();
    }
}
