<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\FavoriteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class FavoriteController extends AbstractController
{
    use ControllerTrait;

    private FavoriteRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(FavoriteRepository $repository, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/favoris', name: 'app_user_favorite_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->getByUser($this->getUserOrThrow());

        $favorites = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('user/favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}
