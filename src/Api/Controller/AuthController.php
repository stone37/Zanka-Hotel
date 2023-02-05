<?php

namespace App\Api\Controller;

use App\Data\PasswordResetRequestData;
use App\Exception\EmailAlreadyUsedException;
use App\Exception\NotVerifiedEmailException;
use App\Exception\OngoingPasswordResetException;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\PasswordService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private PasswordService $resetService,
        private UserRepository $repository,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private DenormalizerInterface $denormalizer,
        private EntityManagerInterface $em,
    )
    {
    }

    public function reset(Request $request): JsonResponse
    {
        $data = json_decode((string) $request->getContent(), true);
        $passwordResetData = $this->denormalizer->denormalize($data, PasswordResetRequestData::class);

        try {
            $this->resetService->resetPassword($passwordResetData);
        } catch (UserNotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessageKey()], Response::HTTP_UNAUTHORIZED);
        } catch (OngoingPasswordResetException $exception) {
            return new JsonResponse(['error' => $exception->getMessageKey()], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }


    public function socialLogin(Request $request): JsonResponse|JWTAuthenticationSuccessResponse|Response
    {
        $content = json_decode((string) $request->getContent(), true);

        $email = $content['email'] ?? null;
        $id = $content['id'] ?? null;
        $service = $content['service'] ?? null;
        $email_verified = $content['email_verified'] ?? null;

        $user = $this->repository->findForOauth($service, $id, $email);

        if (!$user) {
            return $this->json(['data' => 'Aucun utilisateur trouver'], Response::HTTP_ACCEPTED);
        }

        if ($service == 'facebook') {
            if ($user && null === $user->getFacebookId()) {
                throw new EmailAlreadyUsedException();
            }

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        } else {
            if (true !== $email_verified) {
                throw new NotVerifiedEmailException();
            }

            if ($user && null === $user->getGoogleId()) {
                $user->setGoogleId($id);
                $this->em->flush();
            }

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        }
    }
}
