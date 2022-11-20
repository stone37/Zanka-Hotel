<?php

namespace App\Twig;

use App\Util\PriceUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PriceExtension extends AbstractExtension
{
    private PriceUtil $util;

    public function __construct(PriceUtil $util)
    {
        $this->util = $util;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('app_calculate_price', [$this->util, 'getPrice']),
        ];
    }
}
