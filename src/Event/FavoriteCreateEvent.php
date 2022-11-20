<?php

namespace App\Event;

use App\Entity\Favorite;

class FavoriteCreateEvent
{
    private Favorite $favorite;

    public function  __construct(Favorite $favorite)
    {
        $this->favorite = $favorite;
    }

    public function getFavorite(): Favorite
    {
        return $this->favorite;
    }
}