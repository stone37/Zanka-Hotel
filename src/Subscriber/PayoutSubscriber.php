<?php

namespace App\Subscriber;

use App\Entity\Payout;
use App\Entity\Settings;
use App\Event\PayoutCancelledEvent;
use App\Event\PayoutCompletedEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Repository\PayoutRepository;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PayoutSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private PayoutRepository $repository,
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
            PayoutCompletedEvent::class => 'onCompleted',
            PayoutCancelledEvent::class => 'onCancelled'
        ];
    }

    public function onCompleted(PayoutCompletedEvent $event): void
    {
        $payout = $event->getPayout();
        $payout->setState(Payout::PAYOUT_COMPLETED);

        $this->repository->flush();

        $email = $this->mailer->createEmail('mails/payout/complet.twig', ['payout' => $event->getPayout()])
            ->to($event->getPayout()->getCommande()->getHostel()->getEmail())
            ->subject($this->settings->getName() . ' | Paiement de ventes partenaires');

        $this->mailer->send($email);

        $wording = 'Un paiement de vos ventes a été effectuer';
        $url = $this->urlGenerator->generate('app_partner_payout_index');

        $this->service->notifyUser($payout->getOwner(), $wording, $payout, $url);
    }

    public function onCancelled(PayoutCancelledEvent $event)
    {
        $payout = $event->getPayout();
        $payout->setState(Payout::PAYOUT_CANCEL);

        $this->repository->flush();
    }
}

