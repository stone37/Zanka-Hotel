<?php

namespace App\Controller\Admin;

use App\Entity\Hostel;
use App\Event\AdminCRUDEvent;
use App\Form\HostelAdminType;
use App\Form\Filter\AdminHostelType;
use App\Model\Admin\HostelAdminSearch;
use App\Repository\HostelRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class HostelController extends AbstractController
{
    private HostelRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        HostelRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/hostels', name: 'app_admin_hostel_index')]
    public function index(Request $request)
    {
        $search = new HostelAdminSearch();

        $form = $this->createForm(AdminHostelType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $hostels = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/hostel/index.html.twig', [
            'hostels' => $hostels,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/hostels/{id}/edit', name: 'app_admin_hostel_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Hostel $hostel)
    {
        $form = $this->createForm(HostelAdminType::class, $hostel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($hostel);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            if ($hostel->isEnabled() && $hostel->getEnabledAt() == null) {
                $hostel->setEnabledAt(new DateTime());
            }

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'L\'établissement a été mise à jour');

            return $this->redirectToRoute('app_admin_hostel_index');
        }

        return $this->render('admin/hostel/edit.html.twig', [
            'form' => $form->createView(),
            'hostel' => $hostel,
        ]);
    }

    #[Route(path: '/hostels/{id}/show', name: 'app_admin_hostel_show', requirements: ['id' => '\d+'])]
    public function show(Hostel $hostel)
    {
        return $this->render('admin/hostel/show.html.twig', ['hostel' => $hostel]);
    }

    #[Route(path: '/hostels/{id}/move', name: 'app_admin_hostel_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Hostel $hostel)
    {
        if ($request->query->has('pos')) {
            $pos = ($hostel->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $hostel->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_hostel_index');
    }

    #[Route(path: '/hostels/{id}/delete', name: 'app_admin_hostel_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Hostel $hostel)
    {
        $form = $this->deleteForm($hostel);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($hostel);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($hostel, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'établissement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'établissement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet établissement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $hostel,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/hostels/bulk/delete', name: 'app_admin_hostel_bulk_delete', options: ['expose' => true])]
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
                    $hostel = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($hostel), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($hostel, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les établissements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les établissements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' établissements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet établissement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Hostel $hostel)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_hostel_delete', ['id' => $hostel->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_hostel_bulk_delete'))
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
                ],
            ]
        ];
    }
}


