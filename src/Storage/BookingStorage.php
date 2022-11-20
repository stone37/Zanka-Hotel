<?php

namespace App\Storage;

use App\Data\BookingData;

class BookingStorage
{
    private SessionStorage $storage;

    public function __construct(SessionStorage $storage)
    {
        $this->storage = $storage;
    }

    public function set(BookingData $data): void
    {
        $data = [
            'location' => $data->location,
            'checkin' => $data->duration['checkin'],
            'checkout' => $data->duration['checkout'],
            'adult' => $data->adult,
            'children' => $data->children,
            'room_nbr' => $data->roomNumber
        ];

        $this->storage->set($this->provideKey(), $data);
    }

    public function remove(): void
    {
        $this->storage->remove($this->provideKey());
    }

    public function getBookingData(): BookingData
    {
        if (!$this->has()) {
            return new BookingData();
        }

        $session = $this->get();

        $data = new BookingData();
        $data->location = $session['location'];
        $data->duration = ['checkin' => $session['checkin'], 'checkout' => $session['checkout']];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    public function has(): bool
    {
        return $this->storage->has($this->provideKey());
    }

    public function get(): ?array
    {
        return $this->storage->get($this->provideKey());
    }

    public function init(): ?BookingData
    {
        if (!$this->has()) {
            return null;
        }

        $session = $this->get();

        $data = new BookingData();
        $data->location = $session['location'];
        $data->checkin = $session['checkin'];
        $data->checkout = $session['checkout'];
        $data->adult = $session['adult'];
        $data->children = $session['children'];
        $data->roomNumber = $session['room_nbr'];

        return $data;
    }

    private function provideKey(): string
    {
        return '_app_booking';
    }
}
