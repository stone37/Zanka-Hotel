<?php

namespace App\Calculator;

use App\Entity\Hostel;
use App\Entity\Review;
use App\Manager\ReviewManager;

class AverageRatingCalculator
{
    private ReviewManager $manager;

    public function __construct(ReviewManager $manager)
    {
        $this->manager = $manager;
    }

    public function calculate(Hostel $hostel): float
    {
        $sum = 0;
        $reviewsNumber = 0;
        $rating = 0;
        $reviews = $hostel->getReviews();

        /** @var Review $review */
        foreach ($reviews as $review) {
            if ($review->isEnabled()) {
                ++$reviewsNumber;

                //$sum += $this->manager->note($review);
                $sum += $review->getRating();
            }
        }

        if (0 !== $reviewsNumber) {
            $rating = $sum / $reviewsNumber;
        }

        return round($rating, 1);
    }
}
