<?php


namespace App\Api\Controller;


use App\Entity\Booking;
use App\Entity\User;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserCancelledBooking extends AbstractController
{
    public function __construct(private BookingRepository $repository)
    {
    }

    public function __invoke(User $user)
    {
        return $this->repository->getByApi($user, Booking::CANCELLED);
    }
}
