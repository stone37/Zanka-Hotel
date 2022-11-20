<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Event\PartnerCreatedEvent;
use App\Form\Filter\AdminUserType;
use App\Form\RegistrationAdminPartnerType;
use App\Model\Admin\UserSearch;
use App\Repository\UserRepository;
use App\Service\UserBanService;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PartnerController extends AbstractController
{
    private UserRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: '/partners', name: 'app_admin_partner_index')]
    public function index(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdminPartners($search);

        $partners = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners,
            'searchForm' => $form->createView(),
            'type' => 1,
        ]);
    }

    #[Route(path: '/partners/no-confirm', name: 'app_admin_partner_no_confirm_index')]
    public function indexN(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getPartnerNoConfirmed($search);

        $partners = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners,
            'searchForm' => $form->createView(),
            'type' => 2,
        ]);
    }

    #[Route(path: '/partners/deleted', name: 'app_admin_partner_deleted_index')]
    public function indexD(Request $request)
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class);

        $form->handleRequest($request);

        $qb = $this->repository->getPartnerDeleted($search);

        $partners = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/partner/index.html.twig', [
            'partners' => $partners,
            'searchForm' => $form->createView(),
            'type' => 3,
        ]);
    }

    #[Route(path: '/partners/create', name: 'app_admin_partner_create')]
    public function create(Request $request)
    {
        $partner = new User();
        $partner->setRoles(['ROLE_USER', 'ROLE_PARTNER']);

        $form = $this->createForm(RegistrationAdminPartnerType::class, $partner);

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

            $this->addFlash('success', 'Un compte partenaire a été crée');

            return $this->redirectToRoute('app_admin_partner_index');
        }

        return $this->render('admin/partner/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/partners/{id}/edit', name: 'app_admin_partner_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $partner)
    {
        $form = $this->createForm(RegistrationAdminPartnerType::class, $partner);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('plainPassword')->getData()) {
                $partner->setPassword(
                    $form->has('plainPassword') ? $this->passwordHasher->hashPassword(
                        $partner,
                        $form->get('plainPassword')->getData()
                    ) : ''
                );
            }

            $this->repository->flush();

            if ($form->get('plainPassword')->getData()) {
                $this->dispatcher->dispatch(new PartnerCreatedEvent($partner, $form->get('plainPassword')->getData()));
            }

            $this->addFlash('success', 'Un compte partenaire a été mise à jour');

            return $this->redirectToRoute('app_admin_partner_index');
        }

        return $this->render('admin/partner/edit.html.twig', [
            'form' => $form->createView(),
            'partner' => $partner
        ]);
    }

    #[Route(path: '/partners/{id}/show/{type}', name: 'app_admin_partner_show', requirements: ['id' => '\d+', 'type' => '\d+'])]
    public function show(User $partner, $type)
    {
       return $this->render('admin/partner/show.html.twig', ['partner' => $partner, 'type' => $type]);
    }

    #[Route(path: '/partner/{id}/ban', name: 'app_admin_partner_ban', requirements: ['id' => '\d+'])]
    public function ban(Request $request, UserBanService $banService, User $partner)
    {
        $banService->ban($partner);
        $this->repository->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        $this->addFlash('success', "L'utilisateur a été banni");

        return $this->redirectToRoute('app_admin_partner_index');
    }

    #[Route(path: '/partners/{id}/delete', name: 'app_admin_partner_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, User $partner)
    {
        $form = $this->deleteForm($partner);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($partner);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($partner, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le compte partenaire a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le compte partenaire n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $partner,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/partners/bulk/delete', name: 'app_admin_partner_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $partner = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($partner), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($partner, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les comptes partenaires ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les partenaires n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' comptes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(User $partner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_partner_delete', ['id' => $partner->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_partner_bulk_delete'))
            ->getForm();
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

