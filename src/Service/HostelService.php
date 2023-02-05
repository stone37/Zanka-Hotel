<?php

namespace App\Service;

use App\Entity\Hostel;
use App\Entity\User;
use App\Event\HostelDeleteRequestEvent;
use App\Exception\TooManyHostelCreatedException;
use App\Repository\HostelRepository;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HostelService
{
    public const DAYS = 10;

    private HostelRepository $hostelRepository;
    private UniqueNumberGenerator $generator;
    private EventDispatcherInterface $dispatcher;

    public  function __construct(
        HostelRepository $hostelRepository,
        EventDispatcherInterface $dispatcher,
        UniqueNumberGenerator $generator)
    {
        $this->hostelRepository = $hostelRepository;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
    }

    public function createHostel(User $user): Hostel
    {
        if ($this->hostelRepository->getNumberByUser($user) >= $user->getHostelNumber()) {
            throw new TooManyHostelCreatedException();
        }

        return (new Hostel())
            ->setOwner($user)
            ->setEnabled(false)
            ->setReference($this->generator->generate(14));
    }

    public function deleteHostel(Hostel $hostel): void
    {
        $this->dispatcher->dispatch(new HostelDeleteRequestEvent($hostel));

        $hostel->setDeleteAt(new DateTimeImmutable('+ '.(string) self::DAYS.' days'));

        $this->hostelRepository->flush();
    }
}

