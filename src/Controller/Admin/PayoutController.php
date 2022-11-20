<?php

namespace App\Controller\Admin;

use App\Entity\Payout;
use App\Form\Filter\AdminPayoutType;
use App\Model\Admin\PayoutSearch;
use App\Repository\PayoutRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request)
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
    public function report(Request $request)
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
    public function pay(Request $request, Payout $payout)
    {
        $form = $this->payForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /*$event = new AdminCRUDEvent($payout);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($payout, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);*/

                $this->addFlash('success', 'Le paiement a été effectué');
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
    public function cancel(Request $request, Payout $payout)
    {
        $form = $this->cancelForm($payout);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
               /* $event = new AdminCRUDEvent($payment);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($payment, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);*/

                $this->addFlash('success', 'Le paiement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pas pu être abandonné !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
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
    public function payBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->payMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $payout = $this->repository->find($id);
                    /*$this->dispatcher->dispatch(new AdminCRUDEvent($payout), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($payout, false);*/
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les paiements ont été effectué');
            } else {
                $this->addFlash('error', 'Désolé, les paiements n\'ont pas pu être effectué !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir effectuer ces '.count($ids).' paiements ?';
        else
            $message = 'Être vous sur de vouloir effectuer cet paiement ?';

        $render = $this->render('Ui/Modal/_pay_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function payForm(Payout $payout)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_pay', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function cancelForm(Payout $payout)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_cancel', ['id' => $payout->getId()]))
            ->getForm();
    }

    private function payMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payout_bulk_pay'))
            ->getForm();
    }

    private function configuration()
    {
        return [
            'modal' => [
                'pay' => [
                    'type' => 'modal-primary',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-primary',
                    'no_class' => 'btn-primary'
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




