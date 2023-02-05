<?php

namespace App\Subscriber;

use App\Calculator\PayoutCalculator;
use App\Calculator\RoomPriceCalculator;
use App\Entity\Cancelation;
use App\Entity\CancelPayout;
use App\Entity\Commande;
use App\Entity\Payout;
use App\Entity\Room;
use App\Entity\Settings;
use App\Event\BookingCancelledEvent;
use App\Event\BookingCheckEvent;
use App\Event\BookingConfirmedEvent;
use App\Event\BookingPartnerCancelledEvent;
use App\Event\BookingPaymentEvent;
use App\Event\BookingSearchTokenCreatedEvent;
use App\Exception\BookingNotFoundException;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Repository\BookingRepository;
use App\Repository\CancelPayoutRepository;
use App\Repository\PayoutRepository;
use App\Service\NotificationService;
use App\Service\Summary;
use App\Storage\BookingStorage;
use App\Util\HostelUtil;
use App\Util\StringToDateUtil;
use DateTime;
use DateTimeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BookingSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private BookingStorage $storage,
        private BookingRepository $repository,
        private PayoutRepository $payoutRepository,
        private CancelPayoutRepository $cancelPayoutRepository,
        private StringToDateUtil $dateUtil,
        private Mailer $mailer,
        private PayoutCalculator $payoutCalculator,
        private RoomPriceCalculator $roomPriceCalculator,
        private HostelUtil $util,
        private NotificationService $service,
        private UrlGeneratorInterface $urlGenerator,
        private TexterInterface $texter,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BookingCheckEvent::class => 'onChecked',
            BookingConfirmedEvent::class => 'onConfirmed',
            BookingPaymentEvent::class => 'onPayment',
            BookingSearchTokenCreatedEvent::class => 'onSearch',
            BookingPartnerCancelledEvent::class => 'onPartnerCancelled',
            BookingCancelledEvent::class => 'onCancelled'
        ];
    }

    public function onChecked(BookingCheckEvent $event): void
    {
        $room = $event->getRoom();
        $data = $this->storage->getBookingData();
        $room_available = $room->getRoomNumber() - $this->available($room);

        if ($room_available <= 0) {
            throw new BookingNotFoundException();
        }

        if ($data->roomNumber > $room_available)
        {
            $data->roomNumber = (int) $room_available;
            $this->storage->set($data);
        }
    }

    public function onConfirmed(BookingConfirmedEvent $event)
    {
        $booking = $event->getBooking();
        $commande = $booking->getCommande();
        $hostel = $booking->getHostel();
        $summary = new Summary($commande);

        $payout = (new Payout())
            ->setCommande($commande)
            ->setOwner($event->getBooking()->getHostel()->getOwner())
            ->setState(Payout::PAYOUT_NEW)
            ->setCurrency($this->settings->getBaseCurrency()->getCode())
            ->setAmount($this->payoutCalculator->calculate($hostel->getPlan(), $summary->amountPaid(), $summary->amountCommission()));

        $this->payoutRepository->add($payout, true);

        $email = $this->mailer->createEmail('mails/booking/confirm.twig', ['booking' => $booking])
            ->to($booking->getEmail())
            ->subject($this->settings->getName() . ' |  Confirmation de réservation #' . $booking->getReference());

        $this->mailer->send($email);

        $url = $this->urlGenerator->generate('app_admin_booking_show', ['id' => $booking->getId(), 'type' => 2]);
        $this->service->notifyChannel('admin', 'Une réservation a été confirmée', $booking, $url);

        if ($booking->getOwner()) {
            $wording = 'Votre réservation a été confirmée';
            $url = $this->urlGenerator->generate('app_user_booking_confirmed_index');
            $this->service->notifyUser($booking->getOwner(), $wording, $booking, $url);
        }

        /*$wording = "Votre réservation pour %s les %s est confirmée. Numéro de réservation: %s. Bienvenue chez %s !";
        $date = $booking->getCheckin()->format('d/m/Y') . ' - ' . $booking->getCheckout()->format('d/m/Y');

        $sms = new SmsMessage(
            $booking->getPhone(),
            sprintf($wording, $booking->getHostel()->getName(), $date, strtoupper($booking->getReference()), $this->settings->getName())
        );

        $this->texter->send($sms);*/
    }

    public function onPartnerCancelled(BookingPartnerCancelledEvent $event)
    {
        $booking = $event->getBooking();
        $commande = $booking->getCommande();
        $summary = new Summary($commande);

        $this->removePayout($commande);

        // Remboursement complet
        $payout = (new CancelPayout())
            ->setCommande($commande)
            ->setState(CancelPayout::PAYOUT_NEW)
            ->setRole(CancelPayout::PAYOUT_ROLE_PARTNER)
            ->setOwner($booking->getOwner())
            ->setCurrency($this->settings->getBaseCurrency()->getCode())
            ->setAmount($summary->amountPaid());

        $this->cancelPayoutRepository->add($payout, true);

        $email = $this->mailer->createEmail('mails/booking/partner-cancel.twig', ['booking' => $booking])
            ->to($booking->getEmail())
            ->subject($this->settings->getName() . ' | Annulation de votre réservation ');

        $this->mailer->send($email);

        $wording = 'Le partenaire %s a annulée une reservation';
        $url = $this->urlGenerator->generate('app_admin_booking_show', ['id' => $booking->getId(), 'type' => 3]);
        $this->service->notifyChannel('admin', sprintf($wording, "<strong>{$booking->getHostel()->getOwner()->getFirstname()}</strong>"), $booking, $url);

        if ($booking->getOwner()) {
            $wording = 'Votre réservation a été annulée';
            $url = $this->urlGenerator->generate('app_user_booking_cancel_index');
            $this->service->notifyUser($booking->getOwner(), $wording, $booking, $url);
        }

        $wording = "Désolé, votre réservation pour %s les %s a été annulée. Nous espérons vous revoir bientôt chez %s !";
        $date = $booking->getCheckin()->format('d/m/Y') . ' - ' . $booking->getCheckout()->format('d/m/Y');

        /*$sms = new SmsMessage(
            $booking->getPhone(),
            sprintf($wording, $booking->getHostel()->getName(), $date, $this->settings->getName())
        );

        $this->texter->send($sms);*/
    }

    public function onCancelled(BookingCancelledEvent $event)
    {
        $booking = $event->getBooking();
        $commande = $booking->getCommande();
        $hostel = $booking->getHostel();
        $summary = new Summary($commande);

        $this->removePayout($commande);

        $cancellation = $this->getCancellation($booking->getCancelation(), $booking->getCheckin());

        if ($cancellation) {
            // Remboursement complet
            $payout = (new CancelPayout())
                ->setCommande($commande)
                ->setState(CancelPayout::PAYOUT_NEW)
                ->setRole(CancelPayout::PAYOUT_ROLE_USER)
                ->setOwner($event->getBooking()->getOwner())
                ->setCurrency($this->settings->getBaseCurrency()->getCode())
                ->setAmount($summary->amountPaid());

            $this->cancelPayoutRepository->add($payout, true);
        } else {
            if ($hostel->getCancellationPolicy()->getResult() === Cancelation::CANCEL_RESULT_FIRST) {
                // Remboursement partiel

                $partnerAmount = $this->roomPriceCalculator->calculate($booking->getRoom()) * $booking->getRoomNumber();
                $partnerTaxeAmount = $this->roomPriceCalculator->getTaxe($booking->getRoom()) * $booking->getRoomNumber();
                $userAmount = $summary->amountPaid() - $partnerAmount;

                // Refund payout
                $cancelPayout = (new CancelPayout())
                    ->setCommande($commande)
                    ->setRole(CancelPayout::PAYOUT_ROLE_USER)
                    ->setState(CancelPayout::PAYOUT_NEW)
                    ->setOwner($event->getBooking()->getOwner())
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($userAmount);

                // Partner payout
                $payout = (new Payout())
                    ->setCommande($commande)
                    ->setOwner($event->getBooking()->getHostel()->getOwner())
                    ->setState(Payout::PAYOUT_NEW)
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($this->payoutCalculator->calculate($hostel->getPlan(), $partnerAmount, ($partnerAmount - $partnerTaxeAmount)));

                $this->cancelPayoutRepository->add($cancelPayout, true);

            } else {
                // Aucun remboursement
                $payout = (new Payout())
                    ->setCommande($commande)
                    ->setOwner($event->getBooking()->getHostel()->getOwner())
                    ->setState(Payout::PAYOUT_NEW)
                    ->setCurrency($this->settings->getBaseCurrency()->getCode())
                    ->setAmount($this->payoutCalculator->calculate($hostel->getPlan(), $summary->amountPaid(), $summary->amountCommission()));
            }

            $this->payoutRepository->add($payout, true);
        }

        $email = $this->mailer->createEmail('mails/booking/user-cancel.twig', ['booking' => $event->getBooking()])
            ->to($event->getBooking()->getEmail())
            ->subject($this->settings->getName() . ' | Demande d\'annulation de réservation');

        $this->mailer->send($email);

        $wording = 'L\'utilisateur %s a annulée une reservation';
        $url = $this->urlGenerator->generate('app_admin_booking_show', ['id' => $booking->getId(), 'type' => 3]);
        $this->service->notifyChannel('admin', sprintf($wording, "<strong>{$booking->getHostel()->getOwner()->getFirstname()}</strong>"), $booking, $url);
    }

    public function onPayment(BookingPaymentEvent $event)
    {
        $booking = $event->getBooking();

        $email = $this->mailer->createEmail('mails/commande/validate.twig', ['booking' => $booking])
            ->to($booking->getEmail())
            ->subject($this->settings->getName() . ' | Commande validée #' . $booking->getCommande()->getReference());

        $this->mailer->send($email);

        $wording = 'Une réservation a été crée';
        $url = $this->urlGenerator->generate('app_admin_booking_show', ['id' => $booking->getId(), 'type' => 1]);
        $this->service->notifyChannel('admin', $wording, $event->getBooking(), $url);
    }

    public function onSearch(BookingSearchTokenCreatedEvent $event)
    {
        $email = $this->mailer->createEmail('mails/booking/search.twig', [
            'code' => $event->getToken()->getCode()
        ])
            ->to($event->getToken()->getEmail())
            ->subject($this->settings->getName().' | Recherche de reservation');

        $this->mailer->send($email);
    }

    private function available(Room $room): int
    {
        return $this->repository->availableForPeriod(
            $room,
            $this->dateUtil->converter($this->storage->getBookingData()->duration['checkin']),
            $this->dateUtil->converter($this->storage->getBookingData()->duration['checkout'])
        );
    }

    private function getCancellation(Cancelation $cancellation, DateTimeInterface $checkin): ?DateTime
    {
        return $this->util->cancellation($cancellation, $checkin);
    }

    private function removePayout(Commande $commande): void
    {
        $payout = $this->payoutRepository->findOneBy(['commande' => $commande]);

        if ($payout) {
            $this->payoutRepository->remove($payout, true);
        }
    }
}
