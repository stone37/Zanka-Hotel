<?php

namespace App\Service;

use App\Calculator\DaysCalculator;
use App\Calculator\RoomPriceCalculator;
use App\Data\BookingData;
use App\Data\BookingSearchRequestData;
use App\Entity\BookingSearchToken;
use App\Entity\Room;
use App\Entity\User;
use App\Event\BookingSearchTokenCreatedEvent;
use App\Exception\OngoingBookingSearchException;
use App\Repository\BookingSearchTokenRepository;
use App\Storage\BookingStorage;
use App\Util\StringToDateUtil;
use DateTime;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class BookerService
{
    public const EXPIRE_IN = 30; // Temps d'expiration d'un token
    public const INIT_ADULT = 2;
    public const INIT_CHILDREN = 0;
    public const INIT_ROOM = 1;

    private Security $security;
    private BookingStorage $storage;
    private DaysCalculator $daysCalculator;
    private RoomService $roomService;
    private RoomPriceCalculator $priceCalculator;
    private StringToDateUtil $dateUtil;
    private BookingSearchTokenRepository $tokenRepository;
    private EventDispatcherInterface $dispatcher;
    private UniqueNumberGenerator $generator;

    public function __construct(
        BookingStorage $storage,
        Security $security,
        DaysCalculator $daysCalculator,
        RoomService $roomService,
        RoomPriceCalculator $priceCalculator,
        StringToDateUtil $dateUtil,
        BookingSearchTokenRepository $tokenRepository,
        EventDispatcherInterface $dispatcher,
        UniqueNumberGenerator $generator
    ) {
        $this->storage = $storage;
        $this->security = $security;
        $this->daysCalculator = $daysCalculator;
        $this->roomService = $roomService;
        $this->priceCalculator = $priceCalculator;
        $this->dateUtil = $dateUtil;
        $this->tokenRepository = $tokenRepository;
        $this->dispatcher = $dispatcher;
        $this->generator = $generator;
    }

    public function createData(Room $room): BookingData
    {
        $data = $this->storage->getBookingData();
        $night = $this->daysCalculator->getDays($this->getStartDate(), $this->getEndDate());
        $reduced = $room->getPrice() - ($this->getTotalPrice($room) - $this->roomService->getTaxe($room));

        $data->roomId = $room->getId();
        $data->night = $night;
        $data->amount = $this->priceCalculator->calculate($room) * $night * $data->roomNumber;
        $data->taxeAmount = $this->roomService->getTaxe($room) * $night * $data->roomNumber;
        $data->discountAmount = $reduced * $night * $data->roomNumber;

        if ($this->security->getUser()) {

            /** @var User|UserInterface $user */
            $user = $this->security->getUser();

            $data->firstname = (string) $user->getFirstname();
            $data->lastname = (string) $user->getLastname();
            $data->email = (string) $user->getEmail();
            $data->phone = (string) $user->getPhone();
            $data->country = (string) $user->getCountry();
            $data->city = (string) $user->getCity();
        }

        return $data;
    }

    public function add(BookingData $bookingData)
    {
        $this->storage->setData($bookingData);
    }

    public function getTotalPrice(Room $room): int
    {
        return $this->roomService->getPrice($room, $this->getStartDate(), $this->getEndDate()) + $this->roomService->getTaxe($room);
    }

    private function getStartDate(): DateTime
    {
        return $this->dateUtil->converter($this->storage->getBookingData()->duration['checkin']);
    }

    private function getEndDate(): DateTime
    {
        return $this->dateUtil->converter($this->storage->getBookingData()->duration['checkout']);
    }

    public function searchBooking(BookingSearchRequestData $data)
    {
        /** @var BookingSearchToken|null $token */
        $token = $this->tokenRepository->findOneBy(['email' => $data->getEmail()]);

        if (null !== $token && !$this->isExpired($token)) {
            throw new OngoingBookingSearchException();
        }

        if (null === $token) {
            $token = new BookingSearchToken();
            $this->tokenRepository->add($token, false);
        }

        $token->setEmail($data->getEmail())
            ->setCreatedAt(new DateTime())
            ->setCode($this->generator->generate(6, false));

        $this->tokenRepository->flush();

        $this->dispatcher->dispatch(new BookingSearchTokenCreatedEvent($token));
    }

    public function isExpired(BookingSearchToken $token): bool
    {
        $expirationDate = new DateTime('-'.self::EXPIRE_IN.' minutes');

        return $token->getCreatedAt() < $expirationDate;
    }
}

