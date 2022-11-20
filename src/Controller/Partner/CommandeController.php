<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Commande;
use App\Entity\Hostel;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class CommandeController extends AbstractController
{
    use ControllerTrait;

    private CommandeRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(CommandeRepository $repository, PaginatorInterface $paginator)
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/commandes', name: 'app_partner_commande_index')]
    public function index(Request $request)
    {
        $qb = $this->repository->getByPartner($this->getUserOrThrow());

        $orders = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/commande/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route(path: '/commandes/{id}/show', name: 'app_partner_commande_show', requirements: ['id' => '\d+'])]
    public function show(Commande $commande)
    {
        $this->accessDeniedException($commande);

        return $this->render('partner/commande/show.html.twig', [
            'order' => $commande,
        ]);
    }

    #[Route(path: '/commandes/{id}/hostel', name: 'app_partner_commande_hostel', requirements: ['id' => '\d+'])]
    public function byHostel(Request $request, Hostel $hostel)
    {
        $qb = $this->repository->findBy(['hostel' => $hostel], ['createdAt' => 'desc']);

        $orders = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/commande/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    private function accessDeniedException(Commande $commande)
    {
        if ($commande->getHostel()->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }
    }
}


