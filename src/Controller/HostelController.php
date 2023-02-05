<?php

namespace App\Controller;

use App\Controller\RequestDataHandler\HostelListDataHandler;
use App\Controller\RequestDataHandler\HostelSortDataHandler;
use App\Controller\RequestDataHandler\PaginationDataHandler;
use App\Controller\Traits\ControllerTrait;
use App\Data\BookingData;
use App\Entity\Hostel;
use App\Entity\Settings;
use App\Event\HostelListingEvent;
use App\Event\HostelViewEvent;
use App\Exception\CityNotFoundException;
use App\Finder\HostelFinder;
use App\Form\Filter\HostelsFilterType;
use App\Manager\SettingsManager;
use App\Model\HostelSearch;
use App\Repository\CityRepository;
use App\Repository\HostelRepository;
use App\Service\BookerService;
use App\Storage\BookingStorage;
use Knp\Component\Pager\PaginatorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class HostelController extends AbstractController
{
    use ControllerTrait;

    private HostelListDataHandler $hostelListDataHandler;
    private HostelSortDataHandler $hostelSortDataHandler;
    private PaginationDataHandler $paginationDataHandler;
    private BookingStorage $bookingStorage;
    private HostelFinder $hostelFinder;

    private BookingStorage $storage;
    private BookerService $booker;

    private HostelRepository $repository;
    private CityRepository $cityRepository;
    private Breadcrumbs $breadcrumbs;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private ?Settings $settings;

    public function __construct(
        HostelListDataHandler $hostelListDataHandler,
        HostelSortDataHandler $hostelSortDataHandler,
        PaginationDataHandler $paginationDataHandler,
        BookingStorage $bookingStorage,
        HostelFinder $hostelFinder,
        BookingStorage $storage,
        BookerService $booker,
        HostelRepository $repository,
        CityRepository $cityRepository,
        Breadcrumbs $breadcrumbs,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        SettingsManager $manager
    )
    {
        $this->hostelListDataHandler = $hostelListDataHandler;
        $this->hostelSortDataHandler = $hostelSortDataHandler;
        $this->paginationDataHandler = $paginationDataHandler;
        $this->bookingStorage = $bookingStorage;
        $this->hostelFinder = $hostelFinder;
        $this->storage = $storage;
        $this->booker = $booker;
        $this->repository = $repository;
        $this->cityRepository = $cityRepository;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->breadcrumbs = $breadcrumbs;
        $this->settings = $manager->get();
    }

    #[Route(path: '/hostels', name: 'app_hostel_index')]
    public function index(Request $request)
    {
        $this->breadcrumbs($request);
        $this->dispatcher->dispatch(new HostelListingEvent($request));

        $search = new HostelSearch();
        $form = $this->createForm(HostelsFilterType::class, $search);
        $form_mobile = $this->createForm(HostelsFilterType::class, $search);
        $form->handleRequest($request);
        $form_mobile->handleRequest($request);

        $requestData = array_merge(
            array_merge($search->toArray(), $this->hydrate()),
            $request->query->all()
        );

        if ($form->isSubmitted() &&
            !$form->isValid() &&
            !$request->query->has('order_by') &&
            !$request->query->has('sort') &&
            !$request->query->has('limit')) {
            $requestData = $this->clearInvalidEntries($form, $requestData);
        }

        if ($form_mobile->isSubmitted() &&
            !$form_mobile->isValid() &&
            !$request->query->has('order_by') &&
            !$request->query->has('sort') &&
            !$request->query->has('limit')) {
            $requestData = $this->clearInvalidEntries($form, $requestData);
        }

        $data = array_merge(
            $this->hostelListDataHandler->retrieveData($requestData),
            $this->hostelSortDataHandler->retrieveData($requestData),
            $this->paginationDataHandler->retrieveData($requestData)
        );

        $hostels = $this->paginator->paginate(
            $this->repository->getTotalEnabled($data),
            $request->query->getInt('page', $data[PaginationDataHandler::PAGE_INDEX]),
            $data[PaginationDataHandler::LIMIT_INDEX]);

        return $this->render('site/hostel/index.html.twig', [
            'hostels' => $hostels,
            'city' => $data['city'],
            'country' => $data['country'],
            'form' => $form->createView(),
            'form_mobile' => $form_mobile->createView(),
            'search' => $search
        ]);
    }

    #[Route(path: '/hostels/{slug}', name: 'app_hostel_show')]
    public function show(string $slug): Response
    {
        $data = $this->bookingStorage->getBookingData();
        $hostel = $this->repository->getEnabledBySlug($data, $slug);
        $this->showBreadcrumbs($hostel, $data);

        $this->dispatcher->dispatch(new HostelViewEvent($hostel));

        /*$data = $this->storage->getBookingData();

        $form = $this->createForm(BookingDataType::class, $data, [
            'action' => $this->generateUrl('app_hostel_show', ['slug' => $slug])
        ]);*/

        $city = $this->cityRepository->getByName($data->location);

        if (null === $city) {
            throw new CityNotFoundException();
        }

        /*$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data->location = (string) $hostel->getLocation()->getCity();
            $this->booker->add($data);

            return $this->redirectToRoute('app_hostel_show', ['slug' => $slug]);
        }*/

        return $this->render('site/hostel/show.html.twig', [
            'hostel' => $hostel,
            //'form' => $form->createView(),
            'city' => $city->getName(),
            'country' => $city->getCountry()
        ]);
    }

    private function breadcrumbs(Request $request)
    {
        $breadcrumbs = $this->breadcrumb($this->breadcrumbs);
        $breadcrumbs->addItem('Hôtels', $this->generateUrl('app_hostel_index'));

        if ($request->query->get('city')) {
            $breadcrumbs
                ->addItem($request->query->has('city'), $this->generateUrl('app_hostel_index', ['city' => $request->query->get('city')]))
                ->addItem('Résultats de votre recherche');
        }

        return $this->breadcrumbs;
    }

    private function showBreadcrumbs(Hostel $hostel, BookingData $data)
    {
        $this->breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
            ->addItem('Hôtels à ' . strtolower($data->location), $this->generateUrl('app_hostel_index', [
                'adult' => $data->adult,
                'children' => $data->children,
                'checkin' => $data->duration['checkin'],
                'checkout' => $data->duration['checkout'],
                'location' => $data->location,
            ]))
            ->addItem('Les offres d\'hébergement '. $hostel->getName() . ' ('. $hostel->getCategory()->getName() .')');

        return $this->breadcrumbs;
    }

    private function hydrate(): array
    {
        return [
            'location' => $this->bookingStorage->getBookingData()->location,
            'adult' =>  $this->bookingStorage->getBookingData()->adult,
            'children' =>  $this->bookingStorage->getBookingData()->children,
            'checkin' =>  $this->bookingStorage->getBookingData()->duration['checkin'],
            'checkout' => $this->bookingStorage->getBookingData()->duration['checkout']
        ];
    }

    private function clearInvalidEntries(FormInterface $form, array $requestData): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($form->getErrors(true, true) as $error) {
            $errorOrigin = $error->getOrigin();
            $propertyAccessor->setValue(
                $requestData,
                ($errorOrigin->getParent()->getPropertyPath() ?? '') . $errorOrigin->getPropertyPath(),
                ''
            );
        }

        return $requestData;
    }
}
