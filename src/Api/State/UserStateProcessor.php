<?php

namespace App\Api\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Service\TokenGeneratorService;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private UserPasswordHasherInterface $passwordHasher,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private TokenGeneratorService $tokenGenerator
    )
    {
    }

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
            $data->setPassword($hashedPassword);
            $data->eraseCredentials();
        }

        if (!($operation instanceof DeleteOperationInterface) && !$data->getId()) {
            if ($data->getFacebookId() || $data->getGoogleId()) {
                $data->setConfirmationToken(null);

                $user = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

                return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
            } else {
                $data->setConfirmationToken($this->tokenGenerator->generate(60));
            }

            $data->setNotificationsReadAt(new DateTimeImmutable());
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
