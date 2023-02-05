<?php

namespace App\Util;

class ReviewObservationUtil
{
    public function observation(int $rating): string
    {
        if ($rating >= 9.5 && $rating <= 10) {
            return 'Exceptionnel';
        } elseif ($rating >= 9 && $rating < 9.5) {
            return 'Fabuleux';
        } elseif ($rating >= 8 && $rating < 9) {
            return 'Très bien';
        } elseif ($rating >= 7 && $rating < 8) {
            return 'Bien';
        } else {
            return 'Note';
        }
    }
}