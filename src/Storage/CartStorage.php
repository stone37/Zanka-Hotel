<?php

namespace App\Storage;

use App\Entity\Room;
use App\Entity\Supplement;

class CartStorage
{
    private SessionStorage $storage;

    public function __construct(SessionStorage $storage)
    {
        $this->storage = $storage;
    }

    public function get(): ?array
    {
        return $this->storage->get($this->provideKey());
    }

    public function add(Room $room, Supplement $supplement = null): void
    {
        if ($supplement) {
            $this->storage->set($this->provideKey(), ['_room_id' => $room->getId(), '_supplement_id' => $supplement->getId()]);
        } else {
            $this->storage->set($this->provideKey(), ['_room_id' => $room->getId()]);
        }
    }

    public function init(): void
    {
        $this->storage->remove($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_card';
    }
}
