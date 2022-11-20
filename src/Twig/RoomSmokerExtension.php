<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class RoomSmokerExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array(
            new TwigFilter('app_room_smoker', [$this, 'process']),
            new TwigFilter('app_room_smoker_icon', [$this, 'processIcon'])
        );
    }

    public function process(int $smoker): string
    {
        if ($smoker === 1) {
            $response = 'Non-fumeurs';
        } elseif ($smoker === 2) {
            $response = 'Fumeurs';
        } else {
            $response = 'Fumeurs et non-fumeurs';
        }
        return $response;
    }

    public function processIcon(int $smoker)
    {
        if ($smoker === 1) {
            $response = '<i class="fas fa-smoking"></i>';
        } elseif ($smoker === 2) {
            $response = '<i class="fas fa-smoking-ban"></i>';
        } else {
            $response = '<i class="fas fa-smoking mr-1"></i> <i class="fas fa-smoking-ban"></i>';
        }
        return $response;
    }
}