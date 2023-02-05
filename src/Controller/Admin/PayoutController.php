<?php

namespace App\Controller\Admin;

use App\Entity\Payout;
use App\Event\PayoutCancelledEvent;
use App\Event\PayoutCompletedEvent;
use App\Form\Filter\AdminPayoutType;
use App\Model\Admin\PayoutSearch;
use App\Repository\PayoutRepository;
use JetBrains\PhpStorm\ArrayShape;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PayoutController extends AbstractController
{
    private PayoutRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        PayoutRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/payouts', name: 'app_admin_payout_index')]
    public function index(Request $request): Response
    {
        $search = new PayoutSearch();

        $form = $this->createForm(AdminPayoutType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $payouts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/payout/index.html.twig', [
            'payouts' => $payouts,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route(path: '/payouts/report', name: 'app_admin_payout_report')]
    public function report(Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('admin/payout/report.html.twig', [
            'reports' => $this->repository->getMonthlyReport($year),
            'prefix' => 'admin_payout',
            'current_year' => date('Y'),
            'year' => $year,
        ]);
    }

    #[Route(path: '/payouts/{id}/pay', name: 'app_admin_payout_pay', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function pay(Request $request, Payout $payout): RedirectResponse|JsonResponse
    {
        $form = $this->payForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->dispatcher->dispatch(new PayoutCompletedEvent($payout));

            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pu être effectuer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir payé cet partenaire ?';

        $render = $this->render('Ui/Modal/_pay.html.twig', [
            'form' => $form->createView(),
            'data' => $payout,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/payouts/{id}/cancel', name: 'app_admin_payout_cancel', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function cancel(Request $request, Payout $payout): RedirectResponse|JsonResponse
    {
        $form = $this->cancelForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->dispatcher->dispatch(new PayoutCancelledEvent($payout));

            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pas pu être abandonné !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir abandonné cet paiement ?';

        $render = $this->render('Ui/Modal/_cancel.html.twig', [
            'form' => $form->createView(),
            'data' => $payout,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/payouts/bulk/pay', name: 'app_admin_payout_bulk_pay', options: ['expose' => true])]
    public function payBulk(Request $request): RedirectResponse|JsonResponse
    {
        $payouts = $this->repository->findBy(['state' => Payout::PAYOUT_NEW]);

        if (!count($payouts)) {
            return new JsonResponse(0);
        }

        $form = $this->payMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                foreach ($payouts as $payout) {
                    $this->dispatcher->dispatch(new PayoutCompletedEvent($payout));
                }

                $this->addFlash('success', 'Les paiements ont été effectuer avec success');
            } else {
                $this->addFlash('error', 'Désolé, les paiements n\'ont pas pu être effectué');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($payouts) > 1)
            $message = 'Être vous sur de vouloir effectuer ces ' . count($payouts) . ' paiements ?';
        else
            $message = 'Être vous sur de vouloir effectuer cet paiement ?';

        $render = $this->render('Ui/Modal/_pay_multi.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function payForm(Payout $payout): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_pay', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function cancelForm(Payout $payout): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_cancel', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function payMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_bulk_pay'))
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
                ],
            ]
        ];
    }
}




