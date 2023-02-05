<?php

namespace App\Api\Controller;

use App\Entity\Hostel;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserHostelFavorite extends AbstractController
{
    public function __construct(private FavoriteRepository $repository)
    {
    }

    public function __invoke(Hostel $hostel)
    {
        return $this->repository->findOneBy(['hostel' => $hostel, 'owner' => $this->getUser()]);
    }
}
