<?php

namespace App\Twig;

use App\Util\HostelUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
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
        return [
            new TwigFunction('app_city_hostel_nbr', [$this->util, 'cityHostelNumber']),
            new TwigFunction('app_has_created', [$this->util, 'verify']),
            new TwigFunction('app_hostel_cancellation', [$this->util, 'cancellation']),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter('app_hostel_equipment', [$this->util, 'getEquipments'])
        ];
    }
}
