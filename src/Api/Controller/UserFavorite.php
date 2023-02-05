<?php

namespace App\Api\Controller;

use App\Entity\User;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserFavorite extends AbstractController
{
    public function __construct(private FavoriteRepository $repository)
    {
    }

    public function __invoke(User $user)
    {
        return $this->repository->getByUser($user);
    }
}