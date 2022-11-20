<?php

namespace App\Util;

use App\Entity\Banner;
use App\Manager\BannerManager;

class BannerUtil
{
    private BannerManager $manager;

    public function __construct(BannerManager $manager)
    {
        $this->manager = $manager;
    }

    public function hasView(Banner $banner): bool
    {
        return $this->manager->has($banner->getId());
    }
}