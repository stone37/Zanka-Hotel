<?php

namespace App\Twig;

use App\Util\ReviewUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ReviewExtension extends AbstractExtension
{
    public function __construct(private ReviewUtil $util)
    {
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_review_booking', [$this->util, 'getByUser']),
            new TwigFunction('app_review_sequence_note', [$this->util, 'getSequenceNote']),
        );
    }
}