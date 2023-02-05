<?php

namespace App\Api\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAccountChangePassword extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(User $data): ?User
    {
        if ($data->getPlainPassword()) {
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));

            $data->eraseCredentials();
        }

        return $data;
    }
}
