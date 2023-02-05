<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoomNotFoundException extends NotFoundHttpException
{
    public function __construct()
    {
        parent::__construct('L\'hébergement n\'a pas été trouvée ! Veuillez sélectionner un hébergement.');
    }
}
