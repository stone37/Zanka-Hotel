<?php

namespace App\Controller\Admin;

use App\Entity\Plan;
use App\Event\AdminCRUDEvent;
use App\Form\PlanType;
use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PlanController extends AbstractController
{
    private PlanRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        PlanRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/plans', name: 'app_admin_plan_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $plans = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/plan/index.html.twig', [
            'plans' => $plans,
        ]);
    }

    #[Route(path: '/plans/create', name: 'app_admin_plan_create')]
    public function create(Request $request)
    {
        $plan = new Plan();
        $form = $this->createForm(PlanType::class, $plan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($plan);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($plan, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un plan de commission a été crée');

            return $this->redirectToRoute('app_admin_plan_index');
        }

        return $this->render('admin/plan/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/plans/{id}/edit', name: 'app_admin_plan_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Plan $plan): Response
    {
        $form = $this->createForm(PlanType::class, $plan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($plan);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un plan de commission a été mise à jour');

            return $this->redirectToRoute('app_admin_plan_index');
        }

        return $this->render('admin/plan/edit.html.twig', [
            'form' => $form->createView(),
            'plan' => $plan
        ]);
    }

    #[Route(path: '/plans/{id}/delete', name: 'app_admin_plan_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Plan $plan)
    {
        $form = $this->deleteForm($plan);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($plan);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($plan, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le plan de commission a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le plan de commission n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet plan de commission ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $plan,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/plans/bulk/delete', name: 'app_admin_plan_bulk_delete', options: ['expose' => true])]
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
                    $plan = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($plan), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($plan, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les plans de commission ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les plans de commission n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' plans de commission ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet plan de commission ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Plan $plan)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_plan_delete', ['id' => $plan->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_plan_bulk_delete'))
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


