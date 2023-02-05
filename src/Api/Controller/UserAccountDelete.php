<?php

namespace App\Api\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAccountDelete extends AbstractController
{
    public const DAYS = 5;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(User $data): User
    {
        if ($this->passwordHasher->isPasswordValid($data, $data->getPlainPassword() ?? '')) {

            $data->setDeleteAt(new DateTimeImmutable('+ '. self::DAYS .' days'));

            $data->eraseCredentials();
        }

        return $data;
    }
}
