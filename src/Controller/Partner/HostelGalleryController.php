<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Hostel;
use App\Entity\HostelGallery;
use App\Event\AdminCRUDEvent;
use App\Form\GalleryType;
use App\Repository\HostelGalleryRepository;
use App\Repository\HostelRepository;
use App\Service\HostelGalleryService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class HostelGalleryController extends AbstractController
{
    use ControllerTrait;

    private HostelGalleryRepository $repository;
    private HostelRepository $hostelRepository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private HostelGalleryService $service;

    public function __construct(
        HostelGalleryRepository $repository,
        HostelRepository $hostelRepository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        HostelGalleryService $service
    )
    {
        $this->repository = $repository;
        $this->hostelRepository = $hostelRepository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->service = $service;
    }

    #[Route(path: '/hostels/{hostel_id}/gallery', name: 'app_partner_hostel_gallery_index', requirements: ['hostel_id' => '\d+'])]
    public function index(Request $request, $hostel_id)
    {
        /** @var Hostel $hostel */
        $hostel = $this->hostelRepository->find($hostel_id);

        if ($hostel->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }

        $qb = $this->repository->findBy(['hostel' => $hostel], ['position' => 'asc']);

        $galleries = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/gallery/index.html.twig', [
            'galleries' => $galleries,
            'hostel' => $hostel
        ]);
    }

    #[Route(path: '/hostels/{hostel_id}/gallery/add', name: 'app_partner_hostel_gallery_add', requirements: ['hostel_id' => '\d+'], options: ['expose' => true])]
    public function add(Request $request, $hostel_id)
    {
        /** @var Hostel $hostel */
        $hostel = $this->hostelRepository->find($hostel_id);

        if ($hostel->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }

        $this->service->initialize($request);

        $gallery = new HostelGallery();
        $form = $this->createForm(GalleryType::class, $gallery);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $value = $this->service->add($hostel);

            if ($value) {
                $this->addFlash('success', 'Image(s) ajouter à la galerie');
            } else {
                $this->addFlash('info', 'Aucune image sélectionner');
            }

            return $this->redirectToRoute('app_partner_hostel_gallery_index', ['hostel_id' => $hostel_id]);
        }

        return $this->render('partner/gallery/add.html.twig', [
            'form' => $form->createView(),
            'hostel' => $hostel
        ]);
    }

    #[Route(path: '/hostels/{hostel_id}/gallery/{id}/move', name: 'app_partner_hostel_gallery_move', requirements: ['id' => '\d+', 'hostel_id' => '\d+'])]
    public function move(Request $request, HostelGallery $gallery, $hostel_id)
    {
        $this->accessDeniedException($gallery);

        if ($request->query->has('pos')) {
            $pos = ($gallery->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $gallery->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_partner_hostel_gallery_index', ['hostel_id' => $hostel_id]);
    }

    #[Route(path: '/hostels/gallery/{id}/delete', name: 'app_partner_hostel_gallery_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, HostelGallery $gallery)
    {
        $this->accessDeniedException($gallery);

        $form = $this->deleteForm($gallery);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($gallery);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($gallery, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'image a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'image n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette image ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $gallery,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/hostels/gallery/bulk/delete', name: 'app_partner_hostel_gallery_bulk_delete', options: ['expose' => true])]
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
                    $gallery = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($gallery), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($gallery, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les images ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les images n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' images ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette image ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(HostelGallery $gallery)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_hostel_gallery_delete', ['id' => $gallery->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_hostel_gallery_bulk_delete'))
            ->getForm();
    }

    private function accessDeniedException(HostelGallery $gallery)
    {
        if ($gallery->getHostel()->getOwner() !== $this->getUserOrThrow()) {
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