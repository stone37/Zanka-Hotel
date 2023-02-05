<?php

namespace App\Storage;

use App\Data\BookingData;

class BookingStorage
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function getBookingData(): BookingData
    {
        if (!$this->hasData()) {
            return new BookingData();
        }

        $session = $this->getData();

        $data = new BookingData();
        $data->location = $session['location'];
        $data->duration = ['checkin' => $session['checkin'], 'checkout' => $session['checkout']];
        $data->adult = $session['adult'];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    public function initData(): ?BookingData
    {
        if (!$this->hasData()) {
            return new BookingData();
        }

        $session = $this->getData();

        $data = new BookingData();
        $data->location = $session['location'];
        $data->duration = ['checkin' => $session['checkin'], 'checkout' => $session['checkout']];
        $data->adult = $session['adult'];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    public function get(): ?BookingData
    {
        return $this->storage->get($this->provideKey());
    }

    public function has(): bool
    {
        return $this->storage->has($this->provideKey());
    }

    public function set(BookingData $data): void
    {
        $this->setData($data);
        $this->storage->set($this->provideKey(), $data);
    }

    public function remove(): void
    {
        $this->storage->remove($this->provideKey());
    }

    public function getData(): ?array
    {
        return $this->storage->get($this->provideKeyData());
    }

    public function hasData(): bool
    {
        return $this->storage->has($this->provideKeyData());
    }

    public function setData(BookingData $data): void
    {
        $data = [
            'location' => $data->location,
            'checkin' => $data->duration['checkin'],
            'checkout' => $data->duration['checkout'],
            'adult' => $data->adult,
            'children' => $data->children,
            'room_nbr' => $data->roomNumber
        ];

        $this->storage->set($this->provideKeyData(), $data);
    }

    public function removeData(): void
    {
        $this->storage->remove($this->provideKeyData());
    }

    public function getId(): ?int
    {
        return $this->storage->get($this->provideKeyId());
    }

    public function hasId(): bool
    {
        return $this->storage->has($this->provideKeyId());
    }

    public function setId(int $data): void
    {
        $this->storage->set($this->provideKeyId(), $data);
    }

    public function removeId(): void
    {
        $this->storage->remove($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_booking';
    }

    private function provideKeyData(): string
    {
        return '_app_booking_data';
    }

    private function provideKeyId(): string
    {
        return '_app_booking_id';
    }
}
