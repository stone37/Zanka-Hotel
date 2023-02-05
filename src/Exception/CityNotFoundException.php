<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('La ville n\'a pas été trouvée ! Veuillez sélectionner une ville.');
    }
}