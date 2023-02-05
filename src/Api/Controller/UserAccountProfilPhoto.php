<?php

namespace App\Api\Controller;

use App\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UserAccountProfilPhoto extends AbstractController
{
    public function __invoke(User $data, Request $request)
    {
        if (!($data instanceof User)) {
            throw new RuntimeException('Object utilisateur attendu !');
        }

        $data->setFile($request->files->get('file'));

        return $data;
    }
}

