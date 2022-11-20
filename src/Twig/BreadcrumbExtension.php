<?php

namespace App\Twig;

use App\Util\BreadcrumbUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    private BreadcrumbUtil $util;

    public function __construct(BreadcrumbUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('breadcrumb', [$this->util, 'addBreadcrumb'])
        );
    }
}