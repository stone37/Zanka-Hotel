<?php

namespace App\Controller\Admin;

use App\Entity\CancelPayout;
use App\Event\CancelPayoutCancelledEvent;
use App\Event\CancelPayoutCompletedEvent;
use App\Form\Filter\AdminPayoutType;
use App\Model\Admin\PayoutSearch;
use App\Repository\CancelPayoutRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CancelPayoutController extends AbstractController
{
    private CancelPayoutRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        CancelPayoutRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/refunds', name: 'app_admin_cancel_payout_index')]
    public function index(Request $request): Response
    {
        $search = new PayoutSearch();

        $form = $this->createForm(AdminPayoutType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $payouts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/cancelPayout/index.html.twig', [
            'payouts' => $payouts,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route(path: '/refunds/report', name: 'app_admin_cancel_payout_report')]
    public function report(Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('admin/cancelPayout/report.html.twig', [
            'reports' => $this->repository->getMonthlyReport($year),
            'prefix' => 'admin_cancel_payout',
            'current_year' => date('Y'),
            'year' => $year,
        ]);
    }

    #[Route(path: '/refunds/{id}/pay', name: 'app_admin_cancel_payout_pay', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function pay(Request $request, CancelPayout $payout): RedirectResponse|JsonResponse
    {
        $form = $this->payForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->dispatcher->dispatch(new CancelPayoutCompletedEvent($payout));

            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pu être effectuer !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir faire cet remboursement ?';

        $render = $this->render('Ui/Modal/_pay.html.twig', [
            'form' => $form->createView(),
            'data' => $payout,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/refunds/{id}/cancel', name: 'app_admin_cancel_payout_cancel', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function cancel(Request $request, CancelPayout $payout): RedirectResponse|JsonResponse
    {
        $form = $this->cancelForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->dispatcher->dispatch(new CancelPayoutCancelledEvent($payout));

            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pas pu être abandonné !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir abandonné cet remboursement ?';

        $render = $this->render('Ui/Modal/_cancel.html.twig', [
            'form' => $form->createView(),
            'data' => $payout,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/refunds/bulk/pay', name: 'app_admin_cancel_payout_bulk_pay', options: ['expose' => true])]
    public function payBulk(Request $request): RedirectResponse|JsonResponse
    {
        $payouts = $this->repository->findBy(['state' => CancelPayout::PAYOUT_CANCEL]);

        if (!count($payouts)) {
            return new JsonResponse(0);
        }

        $form = $this->payMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($payouts as $payout) {
                    $this->dispatcher->dispatch(new CancelPayoutCompletedEvent($payout));
                }

                $this->addFlash('success', 'Les remboursements ont été effectuer avec success');
            } else {
                $this->addFlash('error', 'Désolé, les paiements n\'ont pas pu être effectué');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($payouts) > 1)
            $message = 'Être vous sur de vouloir effectuer ces ' . count($payouts) . ' remboursements ?';
        else
            $message = 'Être vous sur de vouloir effectuer cet remboursement ?';

        $render = $this->render('Ui/Modal/_cancel_pay_multi.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function payForm(CancelPayout $payout): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_cancel_payout_pay', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function cancelForm(CancelPayout $payout): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_cancel_payout_cancel', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function payMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_cancel_payout_bulk_pay'))
            ->getForm();
    }

    #[ArrayShape(['modal' => "\string[][]"])] private function configuration(): array
    {
        return [
            'modal' => [
                'pay' => [
                    'type' => 'modal-default',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-default',
                    'no_class' => 'btn-default'
                ],
                'cancelled' => [
                    'type' => 'modal-secondary',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-secondary',
                    'no_class' => 'btn-secondary'
                ]
            ]
        ];
    }
}




