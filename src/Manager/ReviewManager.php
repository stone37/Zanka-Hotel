<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Entity\Review;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class ReviewManager
{
    public function __construct(private Security $security, private RequestStack $request)
    {
    }

    public function createNew(Booking $booking): Review
    {
        return (new Review())
            ->setBooking($booking)
            ->setHostel($booking->getHostel())
            ->setLastname($booking->getLastname())
            ->setFirstname($booking->getFirstname())
            ->setEmail($booking->getEmail())
            ->setOwner($this->security->getUser())
            ->setIp($this->request->getMainRequest()->getClientIp());
    }

    #[Pure] public function note(Review $review): float
    {
        $result = ($review->getPersonalRating() + $review->getEquipmentRating() + $review->getPropertyRating() +
                $review->getComfortRating() + $review->getPriceRating() + $review->getLocationRating()) / 6;

        return round($result, 1);
    }

    #[Pure] public function sequenceNote(array $reviews, int $type): float|int
    {
        $rating = 0;
        $item = count($reviews);

        if ($type === 1) {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getPersonalRating();
            }
        } elseif ($type === 2) {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getEquipmentRating();
            }
        } elseif ($type === 3) {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getPropertyRating();
            }
        } elseif ($type === 4) {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getComfortRating();
            }
        } elseif ($type === 5) {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getPriceRating();
            }
        } else {
            /** @var Review $review */
            foreach ($reviews as $review) {
                $rating += $review->getLocationRating();
            }
        }

        return $item !== 0 ? round($rating / $item, 1) : 0;
    }
}

