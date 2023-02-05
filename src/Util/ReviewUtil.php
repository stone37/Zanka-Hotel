<?php

namespace App\Util;

use App\Entity\Booking;
use App\Entity\Hostel;
use App\Manager\BookingManager;
use App\Manager\ReviewManager;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;


class ReviewUtil
{
    public function __construct(
        private BookingManager $bookingManager,
        private ReviewManager $reviewManager,
        private Security $security
    )
    {
    }

    public function getByUser(Hostel $hostel): ?Booking
    {
        return $this->bookingManager->getBookingByUserAndHostel($hostel, $this->security->getUser());
    }

    #[Pure] public function getSequenceNote(array $reviews, int $type): float|int
    {
        return $this->reviewManager->sequenceNote($reviews, $type);
    }
}

