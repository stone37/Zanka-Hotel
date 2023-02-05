<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\HostelConfirmedEvent;
use App\Event\HostelCreatedEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class HostelSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private Mailer $mailer,
        private NotificationService $service,
        private UrlGeneratorInterface $urlGenerator,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HostelCreatedEvent::class => 'onCreated',
            HostelConfirmedEvent::class => 'onConfirmed'
        ];
    }

    public function onCreated(HostelCreatedEvent $event): void
    {
        $hostel = $event->getHostel();

        $wording = 'Le partenaire %s a crée un établissement';
        $url = $this->urlGenerator->generate('app_admin_hostel_show', ['id' => $hostel->getId()]);
        $this->service->notifyChannel('admin', sprintf($wording, "<strong>{$hostel->getOwner()->getFirstname()}</strong>"), $hostel, $url);
    }

    public function onConfirmed(HostelConfirmedEvent $event)
    {
        $hostel = $event->getHostel();

        if (!$hostel->isEnabled()) {
            return;
        }

        $email = $this->mailer->createEmail('mails/hostel/confirm.twig', ['hostel' => $hostel])
            ->to($hostel->getOwner()->getEmail())
            ->subject($this->settings->getName() . ' | Confirmation d\'établissement');

        $this->mailer->send($email);

        $wording = 'Confirmation de votre établissement %s';
        $url = $this->urlGenerator->generate('app_partner_hostel_show', ['id' => $hostel->getId()]);

        $this->service->notifyUser($hostel->getOwner(), sprintf($wording, "<strong>{$hostel->getName()}</strong>"), $hostel, $url);
    }



}

