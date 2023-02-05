<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Hostel;
use App\Event\HostelCreatedEvent;
use App\Exception\TooManyHostelCreatedException;
use App\Form\HostelEquipmentType;
use App\Form\HostelType;
use App\Repository\HostelRepository;
use App\Service\HostelService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class HostelController extends AbstractController
{
    use ControllerTrait;

    private HostelRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private UserPasswordHasherInterface $passwordHasher;
    private HostelService $service;

    public function __construct(
        HostelRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        HostelService $service,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->service = $service;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/hostels', name: 'app_partner_hostel_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->getByPartner($this->getUserOrThrow());

        $hostels = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/hostel/index.html.twig', ['hostels' => $hostels]);
    }

    #[Route(path: '/hostels/create', name: 'app_partner_hostel_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        try {
            $hostel = $this->service->createHostel($this->getUserOrThrow());
        } catch (TooManyHostelCreatedException) {
            $this->addFlash('error', 'Vous avez atteint le quotas de création d\'établissement. Veuillez contacter le service client pourvoir créer plus d\'établissement.');

            return $this->redirectToRoute('app_partner_hostel_index');
        }

        $form = $this->createForm(HostelType::class, $hostel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->add($hostel, true);

            $this->dispatcher->dispatch(new HostelCreatedEvent($hostel));

            $this->addFlash('success', 'Un établissement a été crée');

            return $this->redirectToRoute('app_partner_hostel_index');
        }

        return $this->render('partner/hostel/create.html.twig', [
            'form' => $form->createView(),
            'hostel' => $hostel
        ]);
    }

    #[Route(path: '/hostels/{id}/edit', name: 'app_partner_hostel_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Hostel $hostel)
    {
        $this->accessDeniedException($hostel);

        $form = $this->createForm(HostelType::class, $hostel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->flush();

            $this->addFlash('success', 'Un établissement a été mise à jour');

            return $this->redirectToRoute('app_partner_hostel_index');
        }

        return $this->render('partner/hostel/edit.html.twig', [
            'form' => $form->createView(),
            'hostel' => $hostel,
        ]);
    }

    #[Route(path: '/hostels/{id}/show', name: 'app_partner_hostel_show', requirements: ['id' => '\d+'])]
    public function show(Hostel $hostel)
    {
        $this->accessDeniedException($hostel);

        return $this->render('partner/hostel/show.html.twig', ['hostel' => $hostel]);
    }

    #[Route(path: '/hostels/{id}/equipment', name: 'app_partner_hostel_equipment', requirements: ['id' => '\d+'])]
    public function equipment(Request $request, Hostel $hostel)
    {
        $this->accessDeniedException($hostel);

        $form = $this->createForm(HostelEquipmentType::class, $hostel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->flush();

            $this->addFlash('success', 'Les équipements de l\'établissement a été mise à jour');

            return $this->redirectToRoute('app_partner_hostel_index');
        }

        return $this->render('partner/hostel/equipment.html.twig', [
            'form' => $form->createView(),
            'hostel' => $hostel,
        ]);
    }

    #[Route(path: '/hostels/{id}/delete', name: 'app_partner_hostel_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Hostel $hostel)
    {
        $this->accessDeniedException($hostel);

        $user = $this->getUserOrThrow();

        $form = $this->deleteForm($hostel);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                if (!$this->passwordHasher->isPasswordValid($user, $data['password'] ?? '')) {
                    $this->addFlash('error', 'Impossible de supprimer l\'établissement, mot de passe invalide');

                    return $this->redirectToRoute('app_partner_hostel_index');
                }

                $this->service->deleteHostel($hostel);

                $this->addFlash('error', 'Votre demande de suppression de votre établissement 
                a bien été prise en compte. Votre compte sera supprimé automatiquement au bout de '.HostelService::DAYS.' jours');

                return $this->redirectToRoute('app_partner_hostel_index');
            } else {
                $this->addFlash('error', 'Désolé, l\'établissement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette établissement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $hostel,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Hostel $hostel)
    {
        return $this->createFormBuilder([])
            ->setAction($this->generateUrl('app_partner_hostel_delete', ['id' => $hostel->getId()]))
            ->setMethod('DELETE')
            ->add('password', PasswordType::class, ['attr' => ['placeholder' => 'Entrez votre mot de passer pour confirmer']])
            ->getForm();
    }

    private function accessDeniedException(Hostel $hostel)
    {
        if ($hostel->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }
    }

    private function configuration()
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }
}
