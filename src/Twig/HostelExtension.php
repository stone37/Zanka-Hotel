<?php

namespace App\Twig;

use App\Util\HostelUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HostelExtension extends AbstractExtension
{
    private HostelUtil $util;

    public function __construct(HostelUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_city_hostel_nbr', [$this->util, 'cityHostelNumber'])
        );
    }
}