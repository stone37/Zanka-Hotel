<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use App\Event\AdminCRUDEvent;
use App\Form\BannerType;
use App\Repository\BannerRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class BannerController extends AbstractController
{
    private BannerRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        BannerRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/banners', name: 'app_admin_banner_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy([], ['position' => 'asc']);

        $banners = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/banner/index.html.twig', ['banners' => $banners]);
    }

    #[Route(path: '/banners/create', name: 'app_admin_banner_create')]
    public function create(Request $request)
    {
        $banner = new Banner();

        $form = $this->createForm(BannerType::class, $banner);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($banner);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($banner, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une bannière a été crée');

            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/banners/{id}/edit', name: 'app_admin_banner_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Banner $banner)
    {
        $form = $this->createForm(BannerType::class, $banner);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($banner);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une bannière a été mise à jour');

            return $this->redirectToRoute('app_admin_banner_index');
        }

        return $this->render('admin/banner/edit.html.twig', [
            'form' => $form->createView(),
            'banner' => $banner,
        ]);
    }

    #[Route(path: '/banners/{id}/move', name: 'app_admin_banner_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Banner $banner)
    {
        if ($request->query->has('pos')) {
            $pos = ($banner->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $banner->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_banner_index');
    }

    #[Route(path: '/banners/{id}/delete', name: 'app_admin_banner_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Banner $banner)
    {
        $form = $this->deleteForm($banner);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($banner);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($banner, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La bannière a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la bannière n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette bannière ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $banner,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/banners/bulk/delete', name: 'app_admin_banner_bulk_delete', options: ['expose' => true])]
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
                    $banner = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($banner), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($banner, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les bannières ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les bannières n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' bannières ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette bannière ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Banner $banner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_banner_delete', ['id' => $banner->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_banner_bulk_delete'))
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

