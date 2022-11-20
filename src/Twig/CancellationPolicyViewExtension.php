<?php

namespace App\Twig;

use App\Util\CancellationPolicyViewUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CancellationPolicyViewExtension extends AbstractExtension
{
    private CancellationPolicyViewUtil $util;

    public function __construct(CancellationPolicyViewUtil $util)
    {
        $this->util = $util;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('app_cancellation_view_state', [$this->util, 'convertState']),
            new TwigFilter('app_cancellation_view_action', [$this->util, 'convertAction']),
        ];
    }
}

