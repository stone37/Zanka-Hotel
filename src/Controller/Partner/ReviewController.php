<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Review;
use App\Form\Filter\PartnerReviewType;
use App\Model\Admin\ReviewSearch;
use App\Repository\ReviewRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class ReviewController extends AbstractController
{
    use ControllerTrait;

    private ReviewRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ReviewRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/reviews', name: 'app_partner_review_index')]
    public function index(Request $request)
    {
        $search = new ReviewSearch();

        $form = $this->createForm(PartnerReviewType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getByPartner($search, $this->getUserOrThrow());

        $reviews = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/review/index.html.twig', [
            'reviews' => $reviews,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/reviews/{id}/show', name: 'app_partner_review_show', requirements: ['id' => '\d+'])]
    public function show(Review $review)
    {
        $this->accessDeniedException($review);

        return $this->render('partner/review/show.html.twig', ['review' => $review]);
    }

    private function accessDeniedException(Review $review)
    {
        if ($review->getHostel()->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }
    }
}
