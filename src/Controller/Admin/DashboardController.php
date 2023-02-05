<?php

namespace App\Controller\Admin;

use App\Controller\Traits\ControllerTrait;
use App\Mailing\Mailer;
use App\Repository\BookingRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\PaymentRepository;
use App\Repository\PayoutRepository;
use App\Repository\PlanRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class DashboardController extends AbstractController
{
    use ControllerTrait;

    private UserRepository $userRepository;
    private PaymentRepository $paymentRepository;
    private PayoutRepository $payoutRepository;
    private BookingRepository $bookingRepository;
    private RoomRepository $roomRepository;
    private NewsletterDataRepository $newsletterDataRepository;
    private PlanRepository $planRepository;
    private HttpClientInterface $client;

    public function __construct(
        UserRepository $userRepository,
        PaymentRepository $paymentRepository,
        PayoutRepository $payoutRepository,
        BookingRepository $bookingRepository,
        RoomRepository $roomRepository,
        NewsletterDataRepository $newsletterDataRepository,
        PlanRepository $planRepository,
        HttpClientInterface $client,
    )
    {
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
        $this->payoutRepository = $payoutRepository;
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->newsletterDataRepository = $newsletterDataRepository;
        $this->planRepository = $planRepository;
        $this->client = $client;
    }

    #[Route(path: '/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        /*$sms = new SmsMessage('2250777061569', 'Hello World');
        $sentMessage = $this->texter->send($sms);

        dump($sentMessage);*/

        $taxe = $this->paymentRepository->totalTax();
        $reduction = $this->paymentRepository->totalReduction();
        $sales = $this->paymentRepository->totalRevenues();

        $payment = $this->payoutRepository->totalSent();

        $plan = $this->planRepository->findOneBy(['name' => 'standard', 'enabled' => true]);

        $bookingConfirmNumber = $this->bookingRepository->getConfirmNumber();
        $bookingCancelNumber = $this->bookingRepository->getCancelNumber();
        $bookingArchiveNumber = $this->bookingRepository->getArchiveNumber();

        $today = new DateTime();
        $nextMonth = (new DateTime())->modify('+1 month');
        $roomTotal = $this->roomRepository->getRoomTotalNumber();
        $roomEnabledTotal = $this->roomRepository->getRoomEnabledTotalNumber();
        $roomBookingTotal = $this->bookingRepository->getRoomBookingTotalNumber($today, $nextMonth);

        $bookingTotal = $bookingConfirmNumber+$bookingCancelNumber+$bookingArchiveNumber;
        $bookingCancelPercent = ($bookingTotal > 0) ? ($bookingCancelNumber * 100) / ($bookingTotal) : 0;

        return $this->render('admin/dashboard/index.html.twig', [
            'bookingNewNumber' => $this->bookingRepository->getNewNumber(),
            'bookingConfirmNumber' => $bookingConfirmNumber,
            'bookingCancelNumber' => $bookingCancelNumber,
            'bookingArchiveNumber' => $bookingArchiveNumber,
            'bookingCancelPercent' => round($bookingCancelPercent),
            'users' => $this->userRepository->getUserNumber(),
            'lastClients' => $this->userRepository->getLastClients(),
            'lastOrders' => $this->paymentRepository->getLasts(),
            'newsletterData' => $this->newsletterDataRepository->getNumber(),
            'months' => $this->paymentRepository->getMonthlyRevenues(),
            'days' => $this->paymentRepository->getDailyRevenues(),
            'orders' => $this->paymentRepository->getNumber(),
            'sales' => ($sales - $taxe),
            'reduction' => $reduction,
            'taxe' => $taxe,
            'payment' => $payment,
            'commission' => ((($sales - $taxe) * $plan->getPercent()) / 100),
            'roomTotal' => $roomTotal,
            'roomEnabledTotal' => $roomEnabledTotal,
            'roomBookingTotal' => $roomBookingTotal,
            'today' => $today,
            'nextMonth' => $nextMonth,
            'sms_nbr' => $this->getSmsNbr()
        ]);
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     */
    #[Route(path: '/admin/mailtester', name: 'app_admin_mailtest', methods: ['POST'])]
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', [
            'user' => $this->getUserOrThrow(),
        ])
            ->to($request->get('email'))
            ->subject('Hotel particulier | Confirmation du compte');
        $mailer->send($email);

        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('app_admin_index');
    }

    private function getSmsNbr(): int
    {
        $url = 'https://hsms.ci/api/check-sms/';
        $headers = [
            'Authorization' => 'Bearer ' . $this->getParameter('hsms_token'),
            'Content-Type' => 'application/form-data'
        ];

        $response = $this->client->request('POST', $url, [
            'headers' => $headers,
            'body' => [
                'clientid' => $this->getParameter('hsms_client_id'),
                'clientsecret' => $this->getParameter('hsms_client_secret')
            ]
        ]);

        $response = (array) json_decode($response->getContent());

        return (int) $response[array_key_first($response)];
    }
}

