<?php

namespace App\Subscriber;

use App\Entity\CancelPayout;
use App\Entity\Settings;
use App\Event\CancelPayoutCancelledEvent;
use App\Event\CancelPayoutCompletedEvent;
use App\Event\PaymentRefundedEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Repository\CancelPayoutRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CancelPayoutSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private CancelPayoutRepository $repository,
        private Mailer $mailer,
        private EventDispatcherInterface $dispatcher,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CancelPayoutCompletedEvent::class => 'onCompleted',
            CancelPayoutCancelledEvent::class => 'onCancelled'
        ];
    }

    public function onCompleted(CancelPayoutCompletedEvent $event): void
    {
        $payout = $event->getPayout();
        $payout->setState(CancelPayout::PAYOUT_COMPLETED);

        $this->repository->flush();

        $email = $this->mailer->createEmail('mails/payout/cancel-complet.twig', ['payout' => $event->getPayout()])
            ->to($event->getPayout()->getCommande()->getBooking()->getEmail())
            ->subject($this->settings->getName() . ' | Remboursement de réservation annulée');

        $this->mailer->send($email);

        $this->dispatcher->dispatch(new PaymentRefundedEvent($payout->getCommande()->getPayment()));
    }

    public function onCancelled(CancelPayoutCancelledEvent $event)
    {
        $payout = $event->getPayout();
        $payout->setState(CancelPayout::PAYOUT_CANCEL);

        $this->repository->flush();
    }
}

