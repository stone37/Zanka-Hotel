<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Form\Filter\PartnerPayoutType;
use App\Model\Admin\PayoutSearch;
use App\Repository\PayoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class PayoutController extends AbstractController
{
    use ControllerTrait;

    private PayoutRepository $repository;
    private PaginatorInterface $paginator;

    public function __construct(
        PayoutRepository $repository,
        PaginatorInterface $paginator
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
    }

    #[Route(path: '/payouts', name: 'app_partner_payout_index')]
    public function index(Request $request)
    {
        $search = new PayoutSearch();

        $form = $this->createForm(PartnerPayoutType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getByPartner($search, $this->getUserOrThrow());

        $payouts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/payout/index.html.twig', [
            'payouts' => $payouts,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route(path: '/payouts/report', name: 'app_partner_payout_report')]
    public function report(Request $request)
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('partner/payout/report.html.twig', [
            'reports' => $this->repository->getMonthlyReportByPartner($this->getUserOrThrow(), $year),
            'prefix' => 'admin_payout',
            'current_year' => date('Y'),
            'year' => $year,
        ]);
    }
}




