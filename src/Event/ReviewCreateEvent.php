<?php

namespace App\Event;

use App\Entity\Review;

class ReviewCreateEvent
{
    private Review $review;

    public function  __construct(Review $review)
    {
        $this->review = $review;
    }

    public function getReview(): Review
    {
        return $this->review;
    }
}