<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\BookingCancelledEvent;
use App\Event\BookingPaymentEvent;
use App\Event\PartnerConfirmedEvent;
use App\Event\PartnerCreatedEvent;
use App\Event\PartnerPasswordChangeEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PartnerSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private NotificationService $service,
        private UrlGeneratorInterface $urlGenerator,
        private Mailer $mailer,
        private TexterInterface $texter,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents()
    {
        return [
            PartnerCreatedEvent::class => 'onCreated',
            PartnerConfirmedEvent::class => 'onConfirmed',
            PartnerPasswordChangeEvent::class => 'onPasswordChange',
            BookingPaymentEvent::class => 'onPayment',
            BookingCancelledEvent::class => 'onCancelled'
        ];
    }

    public function onCreated(PartnerCreatedEvent $event)
    {
        $partner = $event->getUser();
        $password = $event->getPassword();

        if (!$partner->isIsVerified()) {
            return;
        }

        $email = $this->mailer->createEmail('mails/partner/new.twig', ['partner' => $partner, 'password' => $password])
            ->to($partner->getEmail())
            ->subject($this->settings->getName() . ' | Confirmation de création de compte');

        $this->mailer->send($email);

        $wording = 'Confirmation de création de votre compte';
        $url = $this->urlGenerator->generate('app_partner_index');

        $this->service->notifyUser($partner, $wording, $partner, $url);
    }

    public function onConfirmed(PartnerConfirmedEvent $event)
    {
        $partner = $event->getUser();

        if (!$partner->isIsVerified()) {
            return;
        }

        if (count($partner->getHostels()) > 0) {
            return;
        }

        $email = $this->mailer->createEmail('mails/partner/confirm.twig', ['partner' => $partner])
            ->to($partner->getEmail())
            ->subject($this->settings->getName() . ' | Confirmation de création de compte');

        $this->mailer->send($email);

        $wording = 'Confirmation de création de votre compte';
        $url = $this->urlGenerator->generate('app_partner_index');

        $this->service->notifyUser($partner, $wording, $partner, $url);
    }

    public function onPasswordChange(PartnerPasswordChangeEvent $event)
    {
        $partner = $event->getUser();
        $password = $event->getPassword();

        $email = $this->mailer->createEmail('mails/partner/password.twig', ['partner' => $partner, 'password' => $password])
            ->to($partner->getEmail())
            ->subject($this->settings->getName() . ' | Changement de mot de passe');

        $this->mailer->send($email);

        $wording = 'Changement de votre mot de passe';
        $url = $this->urlGenerator->generate('app_partner_index');

        $this->service->notifyUser($partner, $wording, $partner, $url);
    }

    public function onPayment(BookingPaymentEvent $event): void
    {
        $booking = $event->getBooking();

        $email = $this->mailer->createEmail('mails/booking/new.twig', ['booking' => $booking])
                ->to($booking->getHostel()->getEmail())
                ->subject($this->settings->getName() . ' | Demande de confirmation de réservation');

        $this->mailer->send($email);

        $wording = 'Vous avez une nouvelle réservation';
        $url = $this->urlGenerator->generate('app_partner_booking_show', ['id' => $booking->getId(), 'type' => 1]);

        $this->service->notifyUser($booking->getHostel()->getOwner(), $wording, $booking, $url);

        /*$wording = "Vous aviez une nouvelle réservation. Merci de la confirmer !";
        $sms = new SmsMessage($booking->getHostel()->getPhone(), sprintf($wording));
        $this->texter->send($sms);*/
    }

    public function onCancelled(BookingCancelledEvent $event)
    {
        $booking = $event->getBooking();

        $email = $this->mailer->createEmail('mails/booking/cancel.twig', ['booking' => $booking])
            ->to($booking->getHostel()->getEmail())
            ->subject($this->settings->getName() . ' | Annulation de réservation');

        $this->mailer->send($email);

        $wording = 'Un réservation a été annuler';
        $url = $this->urlGenerator->generate('app_partner_booking_show', ['id' => $booking->getId(), 'type' => 3]);

        $this->service->notifyUser($booking->getHostel()->getOwner(), $wording, $booking, $url);

       /* $wording = "Le client a malheureusement annuler sa réservation. Numéro de réservation: %s .";
        $sms = new SmsMessage($booking->getHostel()->getPhone(), sprintf($wording, strtoupper($booking->getReference())));
        $this->texter->send($sms);*/
    }
}

