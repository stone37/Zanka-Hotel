<?php

namespace App\Service;

use App\Calculator\AverageRatingCalculator;
use App\Entity\Hostel;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;

class AverageRatingService
{
    private AverageRatingCalculator $averageRatingCalculator;
    private EntityManagerInterface $em;

    public function __construct(
        AverageRatingCalculator $averageRatingCalculator,
        EntityManagerInterface $em
    ) {
        $this->averageRatingCalculator = $averageRatingCalculator;
        $this->em = $em;
    }

    public function update(Hostel $hostel): void
    {
        $this->modifyAverageRating($hostel);
    }

    public function updateFromReview(Review $review): void
    {
        $this->modifyAverageRating($review->getHostel());
    }

    private function modifyAverageRating(Hostel $hostel): void
    {
        $averageRating = $this->averageRatingCalculator->calculate($hostel);

        $hostel->setAverageRating($averageRating);

        $this->em->flush();
    }
}