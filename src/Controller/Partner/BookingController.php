<?php

namespace App\Controller\Partner;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Booking;
use App\Entity\Room;
use App\Event\BookingCancelledEvent;
use App\Event\BookingConfirmedEvent;
use App\Form\Filter\BookingType;
use App\Manager\BookingManager;
use App\Model\Admin\BookingSearch;
use App\Repository\BookingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/p')]
class BookingController extends AbstractController
{
    use ControllerTrait;

    private BookingRepository $repository;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private BookingManager $manager;

    public function __construct(
        BookingRepository $repository,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        BookingManager $manager
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    #[Route(path: '/bookings', name: 'app_partner_booking_index')]
    public function index(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(BookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getByPartner($this->getUserOrThrow(), $search);
        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '1'
        ]);
    }

    #[Route(path: '/bookings/confirmed', name: 'app_partner_booking_confirmed_index')]
    public function confirm(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(BookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getConfirmByPartner($this->getUserOrThrow(), $search);
        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '2'
        ]);
    }

    #[Route(path: '/bookings/cancelled', name: 'app_partner_booking_cancel_index')]
    public function cancel(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(BookingType::class, $search);
        $form->handleRequest($request);

        $this->manager->cancelledAjustement($this->repository->getCancel());

        $qb = $this->repository->getCancelByPartner($this->getUserOrThrow(), $search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '3'
        ]);
    }

    #[Route(path: '/bookings/archive', name: 'app_partner_booking_archive_index')]
    public function archive(Request $request)
    {
        $search = new BookingSearch();

        $form = $this->createForm(BookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getArchiveByPartner($this->getUserOrThrow(), $search);
        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'type' => '4'
        ]);
    }

    #[Route(path: '/bookings/{id}/show/{type}', name: 'app_partner_booking_show', requirements: ['id' => '\d+'])]
    public function show(Booking $booking, $type)
    {
        $this->accessDeniedException($booking);

        return $this->render('partner/booking/show.html.twig', [
            'booking' => $booking,
            'type' => $type,
        ]);
    }

    #[Route(path: '/bookings/{id}/room', name: 'app_partner_booking_room', requirements: ['id' => '\d+'])]
    public function room(Request $request, Room $room)
    {
        if ($room->getHostel()->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }

        $search = new BookingSearch();

        $form = $this->createForm(BookingType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getPartnerByRoom($this->getUserOrThrow(), $room, $search);

        $bookings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('partner/booking/index.html.twig', [
            'bookings' => $bookings,
            'searchForm' => $form->createView(),
            'room' => $room,
            'type' => '5'
        ]);
    }

    #[Route(path: '/bookings/{id}/confirmed', name: 'app_partner_booking_confirmed', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function confirmed(Request $request, Booking $booking)
    {
        $this->accessDeniedException($booking);

        $form = $this->confirmedForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->confirm($booking);

                $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));

                $this->addFlash('success', 'La reservation a été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir confirmer cette reservation ?';

        $render = $this->render('Ui/Modal/_confirm.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/bulk/confirmed', name: 'app_partner_booking_bulk_confirmed', options: ['expose' => true])]
    public function confirmedBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->confirmedMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->repository->find($id);

                    $this->manager->confirm($booking);

                    $this->dispatcher->dispatch(new BookingConfirmedEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été confirmer');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être confirmer !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir confirmer ces '.count($ids).' reservations ?';
        else
            $message = 'Être vous sur de vouloir confirmer cette reservation ?';

        $render = $this->render('Ui/Modal/_confirm_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/{id}/cancelled', name: 'app_partner_booking_cancelled', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function cancelled(Request $request, Booking $booking)
    {
        $this->accessDeniedException($booking);

        $form = $this->cancelledForm($booking);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->cancel($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));

                $this->addFlash('success', 'La reservation a été annuler');
            } else {
                $this->addFlash('error', 'Désolé, la reservation n\'a pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir annuler cette reservation ?';

        $render = $this->render('Ui/Modal/_cancel.html.twig', [
            'form' => $form->createView(),
            'data' => $booking,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/bookings/bulk/cancelled', name: 'app_partner_booking_bulk_cancelled', options: ['expose' => true])]
    public function cancelledBulk(Request $request)
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->cancelledMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $booking = $this->repository->find($id);

                    $this->manager->cancel($booking);

                    $this->dispatcher->dispatch(new BookingCancelledEvent($booking));
                }

                $this->addFlash('success', 'Les reservations ont été annuler');
            } else {
                $this->addFlash('error', 'Désolé, les reservations n\'ont pas pu être annuler !');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir annuler ces '.count($ids).' reservations ?';
        else
            $message = 'Être vous sur de vouloir annuler cette reservation ?';

        $render = $this->render('Ui/Modal/_cancel_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function confirmedForm(Booking $booking)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_booking_confirmed', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function confirmedMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_booking_bulk_confirmed'))
            ->getForm();
    }

    private function cancelledForm(Booking $booking)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_booking_cancelled', ['id' => $booking->getId()]))
            ->getForm();
    }

    private function cancelledMultiForm()
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_partner_booking_bulk_cancelled'))
            ->getForm();
    }

    private function accessDeniedException(Booking $booking)
    {
        if ($booking->getOwner() !== $this->getUserOrThrow()) {
            throw $this->createAccessDeniedException();
        }
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
                ],
                'confirmed' => [
                    'type' => 'modal-success',
                    'icon' => 'fas fa-check',
                    'yes_class' => 'btn-outline-success',
                    'no_class' => 'btn-success'
                ],
                'cancelled' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ],
            ]
        ];
    }
}


