<?php

namespace App\Util;

use App\Entity\Hostel;
use App\Repository\FavoriteRepository;
use Symfony\Component\Security\Core\Security;

class FavoriteUtil
{
    public function __construct(private FavoriteRepository $repository, private Security $security)
    {
    }

    public function verify(Hostel $hostel): bool
    {
        if ($this->security->getUser() === null) {
            return true;
        }

        $favorite = $this->repository->findOneBy(['hostel' => $hostel, 'owner' => $this->security->getUser()]);

        return $favorite ? true : false;
    }
}

