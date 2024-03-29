<?php

namespace App\Service;

use App\Dto\ProfileUpdateDto;
use App\Entity\EmailVerification;
use App\Entity\User;
use App\Event\EmailVerificationEvent;
use App\Exception\TooManyEmailChangeException;
use App\Repository\EmailVerificationRepository;
use App\Security\TokenGeneratorService;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileService
{
    private TokenGeneratorService $tokenGeneratorService;
    private EmailVerificationRepository $emailVerificationRepository;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        TokenGeneratorService $tokenGeneratorService,
        EmailVerificationRepository $emailVerificationRepository,
        EventDispatcherInterface $dispatcher
    ) {
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->emailVerificationRepository = $emailVerificationRepository;
        $this->dispatcher = $dispatcher;
    }

    public function updateProfile(ProfileUpdateDto $data): void
    {
        $data->user->setUsername($data->username);
        $data->user->setLastname($data->lastname);
        $data->user->setFirstname($data->firstname);
        $data->user->setPhone($data->phone);
        $data->user->setCountry($data->country);
        $data->user->setCity($data->city);
        $data->user->setAddress($data->address);

        if ($data->email !== $data->user->getEmail()) {
            $lastRequest = $this->emailVerificationRepository->findLastForUser($data->user);

            if ($lastRequest && $lastRequest->getCreatedAt() > new DateTime('-1 hour')) {
                throw new TooManyEmailChangeException($lastRequest);
            } else {
                if ($lastRequest) {
                    $this->emailVerificationRepository->remove($lastRequest);
                }
            }

            $emailVerification = (new EmailVerification())
                ->setEmail($data->email)
                ->setAuthor($data->user)
                ->setCreatedAt(new DateTime())
                ->setToken($this->tokenGeneratorService->generate());

            $this->emailVerificationRepository->add($emailVerification, false);

            $this->dispatcher->dispatch(new EmailVerificationEvent($emailVerification));
        }
    }

    public function updateEmail(EmailVerification $emailVerification): void
    {
        $emailVerification->getAuthor()->setEmail($emailVerification->getEmail());

        $this->emailVerificationRepository->remove($emailVerification, false);
    }
}
