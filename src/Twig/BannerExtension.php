<?php

namespace App\Twig;

use App\Util\BannerUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BannerExtension extends AbstractExtension
{
    private BannerUtil $util;

    public function __construct(BannerUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_has_banner_view', [$this->util, 'hasView'])
        );
    }
}