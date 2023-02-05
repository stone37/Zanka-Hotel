<?php

namespace App\Api\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserAccountCancel extends AbstractController
{
    public function __invoke(User $data): User
    {
        $data->setDeleteAt(null);

        return $data;
    }
}
