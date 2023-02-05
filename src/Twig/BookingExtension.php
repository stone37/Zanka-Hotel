<?php

namespace App\Twig;

use App\Calculator\DaysCalculator;
use App\Util\BookingUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BookingExtension extends AbstractExtension
{
    public function __construct(
        private DaysCalculator $daysCalculator,
        private BookingUtil $util
    )
    {
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_booking_days_calculator', [$this->daysCalculator, 'getDays']),
            new TwigFunction('app_booking_cancelation_state', [$this->util, 'hasCancelled'])
        );
    }
}