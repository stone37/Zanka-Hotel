<?php

namespace App\Controller\Admin;

use App\Entity\RoomEquipmentGroup;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminEquipmentGroupType;
use App\Form\RoomEquipmentGroupType;
use App\Model\Admin\EquipmentGroupSearch;
use App\Repository\RoomEquipmentGroupRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class RoomEquipmentGroupController extends AbstractController
{
    private RoomEquipmentGroupRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        RoomEquipmentGroupRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/room-equipments-group', name: 'app_admin_room_equipment_group_index')]
    public function index(Request $request)
    {
        $search = new EquipmentGroupSearch();
        $form = $this->createForm(AdminEquipmentGroupType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $equipmentGroups = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/roomEquipmentGroup/index.html.twig', [
            'equipments' => $equipmentGroups,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/room-equipments-group/create', name: 'app_admin_room_equipment_group_create')]
    public function create(Request $request)
    {
        $equipmentGroup = new RoomEquipmentGroup();

        $form = $this->createForm(RoomEquipmentGroupType::class, $equipmentGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipmentGroup);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($equipmentGroup, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un groupe d\'équipement a été crée');

            return $this->redirectToRoute('app_admin_room_equipment_group_index');
        }

        return $this->render('admin/roomEquipmentGroup/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/room-equipments-group/{id}/edit', name: 'app_admin_room_equipment_group_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, RoomEquipmentGroup $equipmentGroup)
    {
        $form = $this->createForm(RoomEquipmentGroupType::class, $equipmentGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($equipmentGroup);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un groupe d\'équipement a été mise à jour');

            return $this->redirectToRoute('app_admin_room_equipment_group_index');
        }

        return $this->render('admin/roomEquipmentGroup/edit.html.twig', [
            'form' => $form->createView(),
            'equipment' => $equipmentGroup,
        ]);
    }

    #[Route(path: '/room-equipments-group/{id}/move', name: 'app_admin_room_equipment_group_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, RoomEquipmentGroup $equipmentGroup)
    {
        if ($request->query->has('pos')) {
            $pos = ($equipmentGroup->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $equipmentGroup->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_room_equipment_group_index');
    }

    #[Route(path: '/room-equipments-group/{id}/delete', name: 'app_admin_room_equipment_group_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, RoomEquipmentGroup $equipmentGroup)
    {
        $form = $this->deleteForm($equipmentGroup);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($equipmentGroup);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($equipmentGroup, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le groupe d\'équipement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le groupe d\'équipement n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet groupe d\'équipement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $equipmentGroup,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/room-equipments-groups/bulk/delete', name: 'app_admin_room_equipment_group_bulk_delete', options: ['expose' => true])]
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
                    $equipment = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($equipment), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($equipment, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les groupes d\'équipements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les groupes d\'équipements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' groupes d\'équipements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet groupe d\'équipement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(RoomEquipmentGroup $equipment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_equipment_group_delete', ['id' => $equipment->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_room_equipment_group_bulk_delete'))
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

