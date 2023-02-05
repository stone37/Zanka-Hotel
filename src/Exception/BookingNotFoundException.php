<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('L\'hébergement sélectionné est complet ! Veuillez sélectionner un autre hébergement.');
    }
}
