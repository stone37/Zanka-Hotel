<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Taxe;
use App\Event\AdminCRUDEvent;
use App\Form\TaxeType;
use App\Repository\TaxeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class TaxeController extends AbstractController
{
    use ControllerTrait;

    private TaxeRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        TaxeRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/taxes', name: 'app_partner_taxe_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy(['owner' => $this->getUserOrThrow()], ['createdAt' => 'desc']);

        $taxes = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/taxe/index.html.twig', ['taxes' => $taxes]);
    }

    #[Route(path: '/taxes/create', name: 'app_partner_taxe_create')]
    public function create(Request $request)
    {
        $taxe = (new Taxe())->setOwner($this->getUserOrThrow());

        $form = $this->createForm(TaxeType::class, $taxe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($taxe);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($taxe, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une taxe a été crée');

            return $this->redirectToRoute('app_partner_taxe_index');
        }

        return $this->render('partner/taxe/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/taxes/{id}/edit', name: 'app_partner_taxe_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Taxe $taxe)
    {
        $this->accessDeniedException($taxe);

        $form = $this->createForm(TaxeType::class, $taxe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($taxe);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une taxe a été mise à jour');

            return $this->redirectToRoute('app_partner_taxe_index');
        }

        return $this->render('partner/taxe/edit.html.twig', [
            'form' => $form->createView(),
            'taxe' => $taxe,
        ]);
    }

    #[Route(path: '/taxes/{id}/delete', name: 'app_partner_taxe_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Taxe $taxe)
    {
        $this->accessDeniedException($taxe);

        $form = $this->deleteForm($taxe);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($taxe);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($taxe, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La taxe a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la taxe n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette taxe ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $taxe,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/taxes/bulk/delete', name: 'app_partner_taxe_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids =  $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $taxe =  $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($taxe), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($taxe, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les taxes ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les taxes n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' taxes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette taxe ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Taxe $taxe)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_taxe_delete', ['id' => $taxe->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_taxe_bulk_delete'))
            ->getForm();
    }

    private function accessDeniedException(Taxe $taxe)
    {
        if ($taxe->getOwner() !== $this->getUserOrThrow()) {
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

