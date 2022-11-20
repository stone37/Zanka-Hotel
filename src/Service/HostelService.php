<?php

namespace App\Service;

use App\Entity\Hostel;
use App\Entity\User;
use App\Event\HostelDeleteRequestEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HostelService
{
    public const DAYS = 10;

    private EntityManagerInterface $em;
    private UniqueNumberGenerator $generator;
    private EventDispatcherInterface $dispatcher;

    public  function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        UniqueNumberGenerator $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
    }

    public function createHostel(User $user): Hostel
    {
        return (new Hostel())
            ->setOwner($user)
            ->setReference($this->generator->generate(10, false));
    }

    public function deleteHostel(Hostel $hostel): void
    {
        $this->dispatcher->dispatch(new HostelDeleteRequestEvent($hostel));

        $hostel->setDeleteAt(new DateTimeImmutable('+ '.(string) self::DAYS.' days'));

        $this->em->flush();
    }
}