<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Event\AdminCRUDEvent;
use App\Form\CategoryType;
use App\Form\Filter\AdminCategoryType;
use App\Model\Admin\CategorySearch;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CategoryController extends AbstractController
{
    private CategoryRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        CategoryRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/categories', name: 'app_admin_category_index')]
    public function index(Request $request)
    {
        $search = new CategorySearch();
        $form = $this->createForm(AdminCategoryType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $categories = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/categories/create', name: 'app_admin_category_create')]
    public function create(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($category, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une catégorie a été crée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/categories/{id}/edit', name: 'app_admin_category_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une catégorie a été mise à jour');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route(path: '/categories/{id}/move', name: 'app_admin_category_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, Category $category)
    {
        if ($request->query->has('pos')) {
            $pos = ($category->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $category->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_category_index');
    }

    #[Route(path: '/categories/{id}/delete', name: 'app_admin_category_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Category $category)
    {
        $form = $this->deleteForm($category);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($category);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($category, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La catégorie a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la catégorie n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cette catégorie ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $category,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/categories/bulk/delete', name: 'app_admin_category_bulk_delete', options: ['expose' => true])]
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
                    $category = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($category), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($category, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les catégories ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les catégories n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' catégories ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette catégorie ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_delete', ['id' => $category->getId()]))
            ->getForm();
    }

    private function deleteMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_bulk_delete'))
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

