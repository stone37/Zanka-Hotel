<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Exception renvoyée si une demande de renvoie de mot de passe est faites alors
 * qu'une demande est déjà en cours.
 */
final class OngoingBookingSearchException extends AuthenticationException
{
    public function __construct()
    {
        parent::__construct('', 0, null);
    }

    public function getMessageKey()
    {
        return 'Ongoing booking search.';
    }
}
