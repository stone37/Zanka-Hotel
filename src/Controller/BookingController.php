<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\BookingData;
use App\Entity\Hostel;
use App\Entity\Room;
use App\Event\BookingCheckEvent;
use App\Exception\RoomNotFoundException;
use App\Form\BookingDataCheckType;
use App\Form\BookingDataType;
use App\Form\BookingType;
use App\Form\DiscountType;
use App\Repository\CommandeRepository;
use App\Repository\SupplementRepository;
use App\Service\BookerService;
use App\Service\RoomService;
use App\Service\Summary;
use App\Storage\BookingStorage;
use App\Storage\CartStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BookingController extends AbstractController
{
    use ControllerTrait;

    private BookerService $booker;
    private RoomService $roomService;
    private EntityManagerInterface $em;
    private Breadcrumbs $breadcrumbs;
    private BookingStorage $storage;
    private CartStorage $cartStorage;
    private CommandeRepository $commandeRepository;
    private SupplementRepository $supplementRepository;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        BookerService $booker,
        BookingStorage $storage,
        CartStorage $cartStorage,
        RoomService $roomService,
        EntityManagerInterface $em,
        CommandeRepository $commandeRepository,
        SupplementRepository $supplementRepository,
        Breadcrumbs $breadcrumbs,
        EventDispatcherInterface $dispatcher
    ) {
        $this->booker = $booker;
        $this->cartStorage = $cartStorage;
        $this->roomService = $roomService;
        $this->em = $em;
        $this->commandeRepository = $commandeRepository;
        $this->supplementRepository = $supplementRepository;
        $this->breadcrumbs = $breadcrumbs;
        $this->storage = $storage;
        $this->dispatcher = $dispatcher;
    }

    #[Route(path: '/reservation', name: 'app_booking_index')]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->breadcrumbs($this->storage->getBookingData());
        $room = $this->roomService->getRoom();

        if (null === $room) {
            throw new RoomNotFoundException();
        }

        $booking = $this->booker->createData($room);

        $prepareCommande = $this->forward('App\Controller\CommandeController::prepareCommande', [
            'data' => $booking,
            'room' => $room,
        ]);

        $commande = $this->commandeRepository->find($prepareCommande->getContent());
        $summary = new Summary($commande);

        $bookingForm = $this->createForm(BookingType::class, $booking);
        $discountForm = $this->createForm(DiscountType::class, $commande);

        $bookingForm->handleRequest($request);

        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {

            $this->storage->set($booking);

            return $this->redirectToRoute('app_commande_validate');
        } else if ($bookingForm->isSubmitted()) {
            $this->addFlash('error', 'Un ou plusieurs champs n\'ont pas été renseigne');
        }

        return $this->render('site/booking/index.html.twig', [
            'booking_form' => $bookingForm->createView(),
            'discount_form' => $discountForm->createView(),
            'commande' => $summary,
            'booking' => $booking,
            'room' => $room
        ]);
    }

    #[Route(path: '/reservation/search/form', name: 'app_booking_search_form')]
    public function search(Request $request): RedirectResponse|Response
    {
        $data = $this->hydrate($request);

        $form = $this->createForm(BookingDataType::class, $data, [
            'action' => $this->generateUrl('app_booking_search_form')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->booker->add($data);

            return $this->redirectToRoute('app_hostel_index', [
                'adult' => $data->adult,
                'children' => $data->children,
                'checkin' => $data->duration['checkin'],
                'checkout' => $data->duration['checkout'],
                'location' => $data->location
            ]);
        } else if ($form->isSubmitted()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('site/booking/searchForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function searchHostel(Request $request, Hostel $hostel): RedirectResponse|Response
    {
        $data = $this->storage->getBookingData();

        $form = $this->createForm(BookingDataCheckType::class, $data, [
            'action' => $this->generateUrl('app_hostel_show', ['slug' => $hostel->getSlug()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$data->location = (string) $hostel->getLocation()->getCity();
            $this->booker->add($data);

            return $this->redirectToRoute('app_hostel_show', ['slug' => $hostel->getSlug()]);
        } else if ($form->isSubmitted()) {
            return $this->redirectToRoute('app_hostel_show', ['slug' => $hostel->getSlug()]);
        }

        return $this->render('site/booking/hostel_searchForm.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reservation/{id}/check', name: 'app_booking_check', requirements: ['id' => '\d+'])]
    public function check(Request $request, Room $room)
    {
        $this->dispatcher->dispatch(new BookingCheckEvent($room));

        if ($request->query->has('supplement_id')) {
            $this->cartStorage->add($room, $this->supplementRepository->find($request->query->get('supplement_id')));
        } else {
            $this->cartStorage->add($room);
        }

        return $this->redirectToRoute('app_booking_index');
    }

    private function breadcrumbs(BookingData $data)
    {
        $this->breadcrumb($this->breadcrumbs)
             ->addItem('Hôtels à ' . strtolower($data->location), $this->generateUrl('app_hostel_index', [
                'adult' => $data->adult,
                'children' => $data->children,
                'checkin' => $data->duration['checkin'],
                'checkout' => $data->duration['checkout'],
                'location' => $data->location
            ]))
            ->addItem('Réservation');

        return $this->breadcrumbs;
    }

    private function hydrate(Request $request): BookingData
    {
        $data = $this->storage->getBookingData();

        if ($request->query->has('location')) {
            $data->location = (string) $request->query->get('location');
        }

        $this->booker->add($data);

        return $data;
    }
}
