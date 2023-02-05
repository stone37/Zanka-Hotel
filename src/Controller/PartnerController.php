<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\User;
use App\Event\PartnerCreatedEvent;
use App\Form\PartnerAddType;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class PartnerController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private UserRepository $repository,
        private EventDispatcherInterface $dispatcher,
        private UserPasswordHasherInterface $passwordHasher,
        private Breadcrumbs $breadcrumbs
    )
    {
    }

    #[Route(path: '/add-hostels', name: 'app_partner_create')]
    public function create(Request $request)
    {
        $partner = new User();
        $partner->setRoles(['ROLE_USER', 'ROLE_PARTNER']);

        $form = $this->createForm(PartnerAddType::class, $partner);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $partner->setPassword(
                $form->has('plainPassword') ? $this->passwordHasher->hashPassword(
                    $partner,
                    $form->get('plainPassword')->getData()
                ) : ''
            );

            $partner->setCreatedAt(new DateTime());
            $partner->setNotificationsReadAt(new DateTimeImmutable());

            $this->repository->add($partner, true);

            $this->dispatcher->dispatch(new PartnerCreatedEvent($partner, $form->get('plainPassword')->getData()));

            return $this->redirectToRoute('app_partner_create_success');
        }

        return $this->render('site/partner/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/add-hostels/success', name: 'app_partner_create_success')]
    public function success()
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Votre demande de partenariat a été reçu');

        return $this->render('site/partner/success.html.twig');
    }
}
