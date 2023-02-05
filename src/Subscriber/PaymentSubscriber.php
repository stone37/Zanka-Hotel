<?php

namespace App\Subscriber;

use App\Data\BookingData;
use App\Entity\Booking;
use App\Entity\Payment;
use App\Entity\Room;
use App\Event\BookingPaymentEvent;
use App\Event\PaymentEvent;
use App\Repository\RoomRepository;
use App\Service\Summary;
use App\Service\UniqueNumberGenerator;
use App\Storage\BookingStorage;
use App\Util\StringToDateUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaymentSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private UniqueNumberGenerator $generator;
    private StringToDateUtil $stringToDateUtil;
    private RequestStack $request;
    private EventDispatcherInterface $dispatcher;
    private BookingStorage $bookingStorage;

    public function __construct(
        RequestStack $request,
        EntityManagerInterface $em,
        UniqueNumberGenerator $generator,
        StringToDateUtil $stringToDateUtil,
        EventDispatcherInterface $dispatcher,
        BookingStorage $bookingStorage
    )
    {
        $this->request = $request;
        $this->em = $em;
        $this->generator = $generator;
        $this->stringToDateUtil = $stringToDateUtil;
        $this->dispatcher = $dispatcher;
        $this->bookingStorage = $bookingStorage;
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentEvent $event)
    {
        $data = $event->getData();
        $commande = $event->getCommande();
        $summary = new Summary($commande);
        $room = $this->roomRepository()->getEnabled($data->roomId);

        $commande->setValidated(true)
            ->setReference($this->generator->generate(10, false));

        // On enregistre la reservation
        $booking = (new Booking())
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setEmail($data->email)
            ->setPhone($data->phone)
            ->setCity($data->city)
            ->setCountry($data->country)
            ->setMessage($data->message)
            ->setCheckin($this->stringToDateUtil->converter($data->duration['checkin']))
            ->setCheckout($this->stringToDateUtil->converter($data->duration['checkout']))
            ->setAdult($data->adult)
            ->setChildren($data->children)
            ->setDays($data->night)
            ->setRoomNumber($data->roomNumber)
            ->setReference($this->generator->generate(8))
            ->setIp($this->request->getMainRequest()->getClientIp())
            ->setAmount($data->amount)
            ->setTaxeAmount($data->taxeAmount)
            ->setDiscountAmount($data->discountAmount)
            ->setHostel($room->getHostel())
            ->setCancelation($room->getHostel()->getCancellationPolicy())
            ->setRoom($room)
            ->setOwner($commande->getOwner())
            ->setCommande($commande);
        $booking = $this->addOccupants($booking, $data);

        // On enregistre la transaction
        $payment = (new Payment())
            ->setCommande($commande)
            ->setPrice($summary->amountPaid())
            ->setTaxe($summary->getTaxeAmount())
            ->setDiscount($summary->getDiscount())
            ->setEnabled(true)
            ->setFirstname($booking->getFirstname())
            ->setLastname($booking->getLastname())
            ->setEmail($booking->getEmail())
            ->setPhone($booking->getPhone())
            ->setCountry($booking->getCountry())
            ->setCity($booking->getCity());

        $this->em->persist($payment);

        $this->em->flush();

        $this->bookingStorage->setId($booking->getId());

        $this->dispatcher->dispatch(new BookingPaymentEvent($booking)); // ecouter pour envoyer notification
    }

    private function roomRepository(): RoomRepository
    {
        return $this->em->getRepository(Room::class);
    }
    private function addOccupants(Booking $booking, BookingData $data): Booking
    {
        $data->getOccupants()->map(function ($occupant) use ($booking) {
            $booking->addOccupant($occupant);
        });

        return $booking;
    }
}



