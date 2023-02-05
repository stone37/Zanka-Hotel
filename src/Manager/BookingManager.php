<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Entity\Hostel;
use App\Entity\User;
use App\Event\BookingPartnerCancelledEvent;
use App\Repository\BookingRepository;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class BookingManager
{
    public function __construct(
        private BookingRepository $repository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function confirm(Booking $booking)
    {
        $this->confirmed($booking);
        $this->repository->flush();
    }

    public function cancel(Booking $booking)
    {
        $this->cancelled($booking);
        $this->repository->flush();
    }

    public function cancelledAjustement(array $bookings)
    {
        if (!$bookings) {
            return;
        }

        /** @var Booking $booking */
        foreach($bookings as $booking) {
            if (!($booking->getStatus() === Booking::CANCELLED)) {
                $this->cancelled($booking);

                $this->dispatcher->dispatch(new BookingPartnerCancelledEvent($booking));
            }
        }

        $this->repository->flush();
    }

    public function getBookingByUserAndHostel(Hostel $hostel, User|UserInterface $user): ?Booking
    {
        return $this->repository->getByUserAndHostel($user, $hostel);
    }

    public function getBookingByReferenceAndHostel(Hostel $hostel, String $reference): ?Booking
    {
        return $this->repository->getByReferenceAndHostel($hostel, $reference);
    }

    private function confirmed(Booking $booking)
    {
        $booking->setStatus(Booking::CONFIRMED);
        $booking->setConfirmedAt(new DateTime());
        $booking->setCancelledAt(null);
    }

    private function cancelled(Booking $booking)
    {
        $booking->setStatus(Booking::CANCELLED);
        $booking->setCancelledAt(new DateTime());
        $booking->setConfirmedAt(null);
    }
}