<?php

namespace App\Controller;

use App\Collector\AppCollector;
use App\Controller\Traits\ControllerTrait;
use App\Converter\CurrencyConverter;
use App\Data\BookingData;
use App\Entity\Commande;
use App\Entity\Room;
use App\Entity\Settings;
use App\Event\PaymentEvent;
use App\Manager\OrderManager;
use App\Manager\SettingsManager;
use App\Repository\BookingRepository;
use App\Repository\CommandeRepository;
use App\Service\Summary;
use App\Storage\BookingStorage;
use App\Storage\CartStorage;
use App\Storage\CommandeStorage;
use FedaPay\Customer;
use FedaPay\FedaPay;
use FedaPay\FedaPayObject;
use FedaPay\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CommandeController extends AbstractController
{
    use ControllerTrait;

    private OrderManager $manager;
    private CommandeStorage $storage;
    private BookingStorage $bookingStorage;
    private CartStorage $cartStorage;
    private CommandeRepository $repository;
    private BookingRepository $bookingRepository;
    private EventDispatcherInterface $dispatcher;
    private Breadcrumbs $breadcrumbs;
    private ?Settings $settings;
    private AppCollector $collector;
    private CurrencyConverter $converter;

    public function __construct(
        OrderManager $manager,
        CommandeStorage $storage,
        BookingStorage $bookingStorage,
        CartStorage $cartStorage,
        CommandeRepository $repository,
        BookingRepository $bookingRepository,
        EventDispatcherInterface $dispatcher,
        Breadcrumbs $breadcrumbs,
        SettingsManager $settingsManager,
        AppCollector $collector,
        CurrencyConverter $converter
    )
    {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->bookingStorage = $bookingStorage;
        $this->cartStorage = $cartStorage;
        $this->repository = $repository;
        $this->bookingRepository = $bookingRepository;
        $this->dispatcher = $dispatcher;
        $this->breadcrumbs = $breadcrumbs;
        $this->settings = $settingsManager->get();
        $this->collector = $collector;
        $this->converter = $converter;
    }

    public function prepareCommande(BookingData $data, Room $room)
    {
        $commande = ($this->manager->getCurrent())
                ->setHostel($room->getHostel())
                ->setValidated(false)
                ->setReference(null)
                ->setAmount($data->amount - $data->taxeAmount)
                ->setTaxeAmount($data->taxeAmount)
                ->setDiscountAmount($data->discountAmount)
                ->setAmountTotal($data->amount - $data->discountAmount);

        if (!$this->storage->has()) {
            $this->repository->add($commande);
        }

        $this->repository->flush();

        $this->storage->set($commande->getId());

        return new Response($commande->getId());
    }

    #[Route(path: '/commande/validate', name: 'app_commande_validate')]
    public function validate()
    {
        $commande = $this->manager->getCurrent();
        $booking = $this->bookingStorage->get();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        if (!$booking instanceof BookingData) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        FedaPay::setApiKey($this->getParameter('fedapay_secret_key'));
        FedaPay::setEnvironment('sandbox'); // setEnvironment('live')

        /** @var Transaction $transaction */
        $transaction = Transaction::create($this->buildRequestBody($commande, $booking));

        /** @var FedaPayObject  $token */
        $token = $transaction->generateToken();

        //return $this->redirect($token->url);

        return $this->redirectToRoute('app_commande_pay');
    }

    #[Route(path: '/commande/payment', name: 'app_commande_pay')]
    public function payment(Request $request)
    {
        FedaPay::setApiKey($this->getParameter('fedapay_secret_key'));
        FedaPay::setEnvironment('sandbox'); // setEnvironment('live')

        /** @var Transaction $transaction */
        //$transaction = Transaction::retrieve($request->query->get('id'));

       /* if ($transaction->status === 'approved') {
            $commande = $this->manager->getCurrent();
            $booking = $this->bookingStorage->get();

            if (!$commande || $commande->isValidated()) {
                throw $this->createNotFoundException('La commande n\'existe pas...');
            }

            if (!$booking instanceof BookingData) {
                throw $this->createNotFoundException('La commande n\'existe pas...');
            }

            $this->dispatcher->dispatch(new PaymentEvent($booking, $commande));

            $this->storage->remove();
            $this->bookingStorage->remove();
            $this->bookingStorage->removeData();
            $this->cartStorage->init();

            return $this->redirectToRoute('app_commande_success');

        } else {
            $this->addFlash('error', 'Désolé, votre commande n\'a pas pu être validée avec succès');

            return $this->redirectToRoute('app_booking_index');
        }*/

        $commande = $this->manager->getCurrent();
        $booking = $this->bookingStorage->get();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        if (!$booking instanceof BookingData) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        $this->dispatcher->dispatch(new PaymentEvent($booking, $commande));

        return $this->redirectToRoute('app_commande_success');
    }

    #[Route(path: '/commande/validate/success', name: 'app_commande_success')]
    public function success()
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Felicitation, votre paiement a été effectué avec succès');

        $booking =  $this->bookingRepository->getEnabled($this->bookingStorage->getId());

        return $this->render('site/commande/success.html.twig', [
            'booking' => $booking,
            'commande' => new Summary($booking->getCommande())
        ]);
    }

    private function buildRequestBody(Commande $commande, BookingData $booking): array
    {
        $callback_url = $this->generateUrl('app_commande_pay', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return [
            'description' => $this->getDescription($commande, $booking),
            'amount' => $this->amountPaid($commande),
            'currency' => ['iso' => $this->collector->getDefaultCurrencyCode()],
            'callback_url' => $callback_url,
            'customer' => [
                'firstname' => $booking->firstname,
                'lastname' => $booking->lastname,
                'email' => $booking->email,
                /*'phone_number' => [
                    'number' => $booking->phone,
                    'country' => 'ci'
                ]*/
            ]
        ];
    }

    private function getDescription(Commande $commande, BookingData $booking): string
    {
        if ($booking->roomNumber > 1) {
            $description = 'Reservation de ' . $booking->roomNumber . ' chambres à ' . strtoupper($commande->getHostel()->getName()) . '.';
        } else {
            $description = 'Reservation d\'une chambre à ' . strtoupper($commande->getHostel()->getName()) . '.';
        }

        return $description;
    }

    private function amountPaid(Commande $commande): int
    {
        $amount = (new Summary($commande))->amountPaid();

        return $this->converter->convert($amount, $this->collector->getCurrencyCode(), $this->collector->getDefaultCurrencyCode());
    }
}

