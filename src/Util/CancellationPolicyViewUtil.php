<?php

namespace App\Util;

use App\Entity\Cancelation;

class CancellationPolicyViewUtil
{
    public function convertState(int $data): string
    {
        if ($data === Cancelation::CANCEL_STATE_ONE_DAY) {
            $result = '1 jour';
        } elseif ($data === Cancelation::CANCEL_STATE_TWO_DAY) {
            $result = '2 jours';
        } elseif ($data === Cancelation::CANCEL_STATE_THREE_DAY) {
            $result = '3 jours';
        } elseif ($data === Cancelation::CANCEL_STATE_SEVEN_DAY) {
            $result = '7 jours';
        } elseif ($data === Cancelation::CANCEL_STATE_FOURTEEN_DAY) {
            $result = '14 jours';
        } else {
            $result = 'Jusqu\'à la date d\'arrivée (18H00)';
        }

        return $result;
    }

    public function convertAction(int $data): string
    {
        if ($data === Cancelation::CANCEL_RESULT_FIRST) {
            $result = 'Paiement de la première nuit par le client';
        } else {
            $result = 'Paiement de la totalité du séjour par le client';
        }

        return $result;
    }
}

