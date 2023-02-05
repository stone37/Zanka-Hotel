<?php

namespace App\Subscriber;

use App\Entity\Review;
use App\Event\AdminCRUDEvent;
use App\Event\ReviewCreateEvent;
use App\Service\AverageRatingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReviewSubscriber implements EventSubscriberInterface
{
    private AverageRatingService $service;

    public function __construct(AverageRatingService $service)
    {
        $this->service = $service;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReviewCreateEvent::class => 'onCreate',
            AdminCRUDEvent::POST_EDIT => 'onEdit',
            AdminCRUDEvent::POST_DELETE => 'onDelete'
        ];
    }

    public function onCreate(ReviewCreateEvent $event): void
    {
        $this->service->updateFromReview($event->getReview());
    }

    public function onEdit(AdminCRUDEvent $event): void
    {
        $object = $event->getEntity();

        if (!$object instanceof Review) {
            return;
        }

        $this->service->updateFromReview($object);
    }

    public function onDelete(AdminCRUDEvent $event): void
    {
        $object = $event->getEntity();

        if (!$object instanceof Review) {
            return;
        }

        $this->service->updateFromReview($object);
    }
}
