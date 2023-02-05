<?php

namespace App\Api\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserAccount extends AbstractController
{
    public function __invoke()
    {
        $user = $this->getUser();

        if (!($user instanceof User)) {
            return $this->json(['message' => 'Aucun compte trouve'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }
}