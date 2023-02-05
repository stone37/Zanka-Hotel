<?php

namespace App\Util;

use App\Finder\CancellationFinder;

class HostelCancellationUtil
{
    private CancellationFinder $finder;

    public function __construct(CancellationFinder $finder)
    {
        $this->finder = $finder;
    }

    public function getCancellation(int $id): ?array
    {
        return $this->finder->find($id);
    }
}