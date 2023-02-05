<?php

namespace App\Twig;

use App\Util\ReviewObservationUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReviewObservationExtension extends AbstractExtension
{
    private ReviewObservationUtil $util;

    public function __construct(ReviewObservationUtil $util)
    {
        $this->util = $util;
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_review_observation', [$this->util, 'observation'])
        );
    }
}
