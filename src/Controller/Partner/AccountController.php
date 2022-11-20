<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Dto\ProfileUpdateDto;
use App\Exception\TooManyEmailChangeException;
use App\Form\UpdateAvatarForm;
use App\Repository\UserRepository;
use App\Service\ProfileService;
use App\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class AccountController extends AbstractController
{
    use ControllerTrait;

    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $repository;
    private ProfileService $profileService;
    private EntityManagerInterface $em;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $repository,
        ProfileService $profileService,
        EntityManagerInterface $em
    ) {
        $this->passwordEncoder = $passwordHasher;
        $this->repository = $repository;
        $this->profileService = $profileService;
        $this->em = $em;
    }

    #[Route(path: '/profil/edit', name: 'app_partner_profil_edit')]
    public function edit(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUserOrThrow();

        $formUpdate = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        $formAvatarUpdate = $this->createForm(UpdateAvatarForm::class, $user);

        $formUpdate->handleRequest($request);

        try {
            if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
                $data = $formUpdate->getData();
                $this->profileService->updateProfile($data);
                $this->repository->flush();

                if ($user->getEmail() !== $data->email) {
                    $this->addFlash(
                        'success',
                        "Votre profil a bien été mis à jour, un email a été envoyé à {$data->email} pour confirmer votre changement"
                    );
                } else {
                    $this->addFlash('success', 'Votre profil a bien été mis à jour');
                }

                return $this->redirectToRoute('app_user_profil_edit');
            }
        } catch (TooManyEmailChangeException) {
            $this->addFlash('error', "Vous avez déjà un changement d'email en cours.");
        }

        return $this->render('partner/account/edit.html.twig', [
            'form_update' => $formUpdate->createView(),
            'form_avatar' => $formAvatarUpdate->createView(),
            'user' => $user
        ]);
    }

    #[Route(path: '/profil/avatar', name: 'app_user_avatar')]
    public function avatar(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUserOrThrow();

        $form = $this->createForm(UpdateAvatarForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Avatar mis à jour avec succès');
        }

        return $this->redirectToRoute('app_partner_profil_edit');
    }
}
