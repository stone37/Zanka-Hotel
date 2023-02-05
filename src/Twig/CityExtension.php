<?php

namespace App\Twig;

use App\Util\CityUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CityExtension extends AbstractExtension
{
    private CityUtil $util;

    public function __construct(CityUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('app_city_find', [$this->util, 'getCity'])
        ];
    }
}
