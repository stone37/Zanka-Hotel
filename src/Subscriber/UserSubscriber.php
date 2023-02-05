<?php

namespace App\Subscriber;

use App\Event\UserBannedEvent;
use App\Repository\BookingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private BookingRepository $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [UserBannedEvent::class => 'cleanUserContent'];
    }

    public function cleanUserContent(UserBannedEvent $event): void
    {
        $this->bookingRepository->deleteForUser($event->getUser());
    }
}
