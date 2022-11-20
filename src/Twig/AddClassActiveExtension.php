<?php

namespace App\Twig;

use App\Util\AddClassActiveUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AddClassActiveExtension extends AbstractExtension
{
    private AddClassActiveUtil $util;

    public function __construct(AddClassActiveUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('isActive', [$this->util, 'verify'])
        );
    }
}