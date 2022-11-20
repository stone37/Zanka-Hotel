<?php

namespace App\Twig;

use App\Util\ConvertMoneyUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConvertMoneyExtension extends AbstractExtension
{
    private ConvertMoneyUtil $util;

    public function __construct(ConvertMoneyUtil $util)
    {
        $this->util = $util;
    }

    public function getFilters(): array
    {
        return [new TwigFilter('app_convert_money', [$this->util, 'convertAmount'])];
    }
}