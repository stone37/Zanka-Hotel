<?php

namespace App\Subscriber;

use App\Repository\LoginAttemptRepository;
use App\Event\PasswordRecoveredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResetSubscriber implements EventSubscriberInterface
{
    private LoginAttemptRepository $repository;

    public function __construct(LoginAttemptRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [PasswordRecoveredEvent::class => 'onPasswordRecovered'];
    }

    public function onPasswordRecovered(PasswordRecoveredEvent $event): void
    {
        $this->repository->deleteAttemptsFor($event->getUser());
    }
}
