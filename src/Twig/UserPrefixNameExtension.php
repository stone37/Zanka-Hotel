<?php

namespace App\Twig;

use App\Util\UserPrefixNameUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserPrefixNameExtension extends AbstractExtension
{
    private UserPrefixNameUtil $prefix;

    public function __construct(UserPrefixNameUtil $prefix)
    {
        $this->prefix = $prefix;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('userPrefixName', [$this->prefix, 'prefix']),
            new TwigFunction('dataPrefixName', [$this->prefix, 'dataPrefix']),
        ];
    }
}
