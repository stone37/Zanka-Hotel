<?php

namespace App\Api\Controller;

use App\Entity\User;
use App\Event\UserCreatedEvent;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserSocialCreated extends AbstractController
{
    public function __construct(
        private UserRepository $repository,
        private AuthenticationSuccessHandler $authenticationSuccessHandler,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $user = new User();

        $user->setEmail($content['email']);
        $user->setPhone($content['phone']);
        $user->setLastname($content['lastname']);
        $user->setFirstname($content['firstname']);
        $user->setConfirmationToken(null);

        if ($content['service'] == 'facebook') {
            $user->setFacebookId($content['id'] ?? null);
            $user->setGoogleId(null);
        } else {
            $user->setFacebookId(null);
            $user->setGoogleId($content['id'] ?? null);
        }

        $errors = $this->validator->validate($user);

        if (!count($errors)) {
            $this->repository->add($user, true);
            $this->dispatcher->dispatch(new UserCreatedEvent($user, true));

            $user = $this->repository->findForAuth($content['email']);

            return $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        }

        $data = [];

        foreach ($errors as $error) {
            $data[] = $error->getMessage();
        }

        return $this->json(['message' => $data], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
