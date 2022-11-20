<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Supplement;
use App\Event\AdminCRUDEvent;
use App\Form\SupplementType;
use App\Repository\SupplementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class SupplementController extends AbstractController
{
    use ControllerTrait;

    private SupplementRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        SupplementRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/supplements', name: 'app_partner_supplement_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy(['owner' => $this->getUserOrThrow()], ['position' => 'ASC']);

        $supplements = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/supplement/index.html.twig', [
            'supplements' => $supplements,
        ]);
    }

    #[Route(path: '/supplements/create', name: 'app_partner_supplement_create')]
    public function create(Request $request)
    {
        $supplement = (new Supplement())->setOwner($this->getUserOrThrow());

        $form = $this->createForm(SupplementType::class, $supplement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($supplement);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($supplement, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un supplément a été crée');

            return $this->redirectToRoute('app_partner_supplement_index');
        }

        return $this->render('partner/supplement/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/supplements/{id}/edit', name: 'app_partner_supplement_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Supplement $supplement)
    {
        $this->accessDeniedException($supplement);

        $form = $this->createForm(SupplementType::class, $supplement);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($supplement);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un supplément a été mise à jour');

            return $this->redirectToRoute('app_partner_supplement_index');
        }

        return $this->render('partner/supplement/edit.html.twig', [
            'form' => $form->createView(),
            'supplement' => $supplement,
        ]);
    }

    #[Route(path: '/supplements/{id}/delete', name: 'app_partner_supplement_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Supplement $supplement)
    {
        $this->accessDeniedException($supplement);

        $form = $this->deleteForm($supplement);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($supplement);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($supplement, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le supplément a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, supplément n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet supplément ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $supplement,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/supplements/bulk/delete', name: 'app_partner_supplement_bulk_delete', options: ['expose' => true])]
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
                    $supplement = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($supplement), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($supplement, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les suppléments ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les suppléments n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' suppléments ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet suppléments ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Supplement $supplement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_supplement_delete', ['id' => $supplement->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_supplement_bulk_delete'))
            ->getForm();
    }

    private function accessDeniedException(Supplement $supplement)
    {
        if ($supplement->getOwner() !== $this->getUserOrThrow()) {
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
                ],
            ]
        ];
    }

}


