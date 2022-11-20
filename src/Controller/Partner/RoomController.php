<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Bedding;
use App\Entity\Room;
use App\Event\AdminCRUDEvent;
use App\Form\EquipmentRoomType;
use App\Form\Filter\PartnerRoomType;
use App\Form\RoomSupplementType;
use App\Form\RoomType;
use App\Model\Admin\RoomSearch;
use App\Repository\RoomRepository;
use App\Util\RoomNameUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class RoomController extends AbstractController
{
    use ControllerTrait;

    private RoomRepository $repository;
    private EntityManagerInterface $em;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private RoomNameUtil $util;

    public function __construct(
        RoomRepository $repository,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        RoomNameUtil $util
    )
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->util = $util;
    }

    #[Route(path: '/rooms', name: 'app_partner_room_index')]
    public function index(Request $request)
    {
        $search = new RoomSearch();

        $form = $this->createForm(PartnerRoomType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getByPartner($this->getUserOrThrow(), $search);

        $rooms = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/room/index.html.twig', [
            'rooms' => $rooms,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/rooms/show/{id}', name: 'app_partner_room_show', requirements: ['id' => '\d+'])]
    public function show(Room $room)
    {
        $this->accessDeniedException($room);

       return $this->render('partner/room/show.html.twig', ['room' => $room]);
    }

    #[Route(path: '/rooms/create', name: 'app_partner_room_create')]
    public function create(Request $request)
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $room = $this->ajustement($room);

            $event = new AdminCRUDEvent($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($room, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un hébergement a été crée');

            return $this->redirectToRoute('app_partner_room_index');
        }

        return $this->render('partner/room/create.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }

    #[Route(path: '/rooms/{id}/edit', name: 'app_partner_room_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Room $room)
    {
        $this->accessDeniedException($room);

        $originalBedding = new ArrayCollection();

        foreach ($room->getBeddings() as $bedding) {
            $originalBedding->add($bedding);
        }

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Bedding $bedding */
            foreach ($originalBedding as $bedding) {
                if (false === $room->getBeddings()->contains($bedding)) {

                    $bedding->setRoom(null);

                    $this->em->remove($bedding);
                }
            }

            $room = $this->ajustement($room);
            $event = new AdminCRUDEvent($room);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->add($room, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un hébergement a été mise à jour');

            return $this->redirectToRoute('app_partner_room_index');
        }

        return $this->render('partner/room/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/rooms/{id}/equipment', name: 'app_partner_room_equipment', requirements: ['id' => '\d+'])]
    public function equipment(Request $request, Room $room)
    {
        $this->accessDeniedException($room);

        $form = $this->createForm(EquipmentRoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->flush();

            $this->addFlash('success', 'Les équipements de l\'hébergement a été mise à jour');

            return $this->redirectToRoute('app_partner_room_index');
        }

        return $this->render('partner/room/equipment.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/rooms/{id}/supplement', name: 'app_partner_room_supplement', requirements: ['id' => '\d+'])]
    public function supplement(Request $request, Room $room)
    {
        $this->accessDeniedException($room);

        $form = $this->createForm(RoomSupplementType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->flush();

            $this->addFlash('success', 'Les supplements de l\'hébergement a été mise à jour');

            return $this->redirectToRoute('app_partner_room_index');
        }

        return $this->render('partner/room/supplement.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
        ]);
    }

    #[Route(path: '/rooms/{id}/delete', name: 'app_partner_room_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Room $room)
    {
        $this->accessDeniedException($room);

        $form = $this->deleteForm($room);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($room);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($room, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'hébergement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'hébergement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet hébergement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $room,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/rooms/bulk/delete', name: 'app_partner_room_bulk_delete', options: ['expose' => true])]
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
                    $room = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($room), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($room, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les hébergements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les hébergements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' hébergements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet hébergement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Room $room)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_room_delete', ['id' => $room->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_room_bulk_delete'))
            ->getForm();
    }

    private function accessDeniedException(Room $room)
    {
        if ($room->getHostel()->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }
    }

    private function ajustement(Room $room): Room
    {
        $room->setName($this->util->getName($room));

        if (($room->getType() === 'Suite') || !($room->getType() === 'Appartement')) {
            $room->setDataRoomNumber(null);
            $room->setDataLivingRoomNumber(null);
            $room->setDataBathroomNumber(null);
        }

        return $room;
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


