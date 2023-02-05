<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Hostel;
use App\Repository\FavoriteRepository;
use App\Storage\BookingStorage;
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
    private BookingStorage $storage;

    public function __construct(
        BookingStorage $storage,
        FavoriteRepository $repository,
        PaginatorInterface $paginator
    )
    {
        $this->storage = $storage;
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

    #[Route(path: '/favoris/{id}/check', name: 'app_user_favorite_check')]
    public function check(Hostel $hostel)
    {
        $data = $this->storage->getBookingData();
        $data->location = (string) $hostel->getLocation()->getCity();
        $this->storage->setData($data);

        return $this->redirectToRoute('app_hostel_show', ['slug' => $hostel->getSlug()]);
    }
}
