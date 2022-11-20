<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Repository\BookingRepository;
use App\Repository\PaymentRepository;
use App\Repository\PayoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class DashboardController extends AbstractController
{
    use ControllerTrait;

    private BookingRepository $bookingRepository;
    private PaymentRepository $paymentRepository;
    private PayoutRepository $payoutRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        PaymentRepository $paymentRepository,
        PayoutRepository $payoutRepository
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->paymentRepository = $paymentRepository;
        $this->payoutRepository = $payoutRepository;
    }

    #[Route(path: '/', name: 'app_partner_index')]
    public function index(): Response
    {
        $user = $this->getUserOrThrow();

        $bookingNewNumber = $this->bookingRepository->getNewNumber($user);
        $bookingConfirmNumber = $this->bookingRepository->getConfirmNumber($user);
        $bookingCancelNumber = $this->bookingRepository->getCancelNumber($user);
        $bookingArchiveNumber = $this->bookingRepository->getArchiveNumber($user);

        $bookingTotal = $bookingConfirmNumber+$bookingCancelNumber+$bookingArchiveNumber;
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
            'sales' => $this->paymentRepository->totalRevenues($user),
            'payments' => $this->payoutRepository->totalSent($user)
        ]);
    }
}
