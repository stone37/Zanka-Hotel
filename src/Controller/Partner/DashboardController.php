<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Repository\BookingRepository;
use App\Repository\PaymentRepository;
use App\Repository\PayoutRepository;
use App\Repository\RoomRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    use ControllerTrait;

    private BookingRepository $bookingRepository;
    private RoomRepository $roomRepository;
    private PaymentRepository $paymentRepository;
    private PayoutRepository $payoutRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        RoomRepository $roomRepository,
        PaymentRepository $paymentRepository,
        PayoutRepository $payoutRepository
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->paymentRepository = $paymentRepository;
        $this->payoutRepository = $payoutRepository;
    }

    #[Route(path: '/p', name: 'app_partner_index')]
    public function index(): Response
    {
        $user = $this->getUserOrThrow();

        $taxe = $this->paymentRepository->totalTax($user);
        $reduction = $this->paymentRepository->totalReduction($user);
        $sales = $this->paymentRepository->totalRevenues($user);

        $payment = $this->payoutRepository->totalSent($user);

        $bookingNewNumber = $this->bookingRepository->getNewNumber($user);
        $bookingConfirmNumber = $this->bookingRepository->getConfirmNumber($user);
        $bookingCancelNumber = $this->bookingRepository->getCancelNumber($user);
        $bookingArchiveNumber = $this->bookingRepository->getArchiveNumber($user);

        $today = new DateTime();
        $nextMonth = (new DateTime())->modify('+1 month');
        $roomTotal = $this->roomRepository->getRoomByPartnerTotalNumber($user);
        $roomEnabledTotal = $this->roomRepository->getRoomEnabledByPartnerTotalNumber($user);
        $roomBookingTotal = $this->bookingRepository->getRoomBookingTotalByPartnerNumber($user, $today, $nextMonth);

        $bookingTotal = $bookingConfirmNumber + $bookingCancelNumber + $bookingArchiveNumber;
        $bookingCancelPercent = ($bookingTotal > 0) ? ($bookingCancelNumber * 100) / ($bookingTotal) : 0;

        return $this->render('partner/dashboard/index.html.twig', [
            'bookingNewNumber' => $bookingNewNumber,
            'bookingConfirmNumber' => $bookingConfirmNumber,
            'bookingCancelNumber' => $bookingCancelNumber,
            'bookingArchiveNumber' => $bookingArchiveNumber,
            'bookingCancelPercent' => round($bookingCancelPercent),
            'lastOrders' => $this->paymentRepository->getLasts($user),
            'orders' => $this->paymentRepository->getNumber($user),
            'months' => $this->paymentRepository->getMonthlyPartnerRevenues($user),
            'days' => $this->paymentRepository->getDailyPartnerRevenues($user),
            'sales' => ($sales - $taxe),
            'taxe' => $taxe,
            'reduction' => $reduction,
            'payment' => $payment,
            'roomTotal' => $roomTotal,
            'roomEnabledTotal' => $roomEnabledTotal,
            'roomBookingTotal' => $roomBookingTotal,
            'today' => $today,
            'nextMonth' => $nextMonth
        ]);
    }
}
