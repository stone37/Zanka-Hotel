<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class ReviewController extends AbstractController
{
    use ControllerTrait;

    private ReviewRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(ReviewRepository $repository, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/reviews', name: 'app_user_review_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->getByUser($this->getUserOrThrow());

        $reviews = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('user/review/index.html.twig', [
            'reviews' => $reviews
        ]);
    }
}
