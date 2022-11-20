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
use App\Finder\HostelFinder;
use App\Form\Filter\HostelsFilterType;
use App\Form\Filter\HostelType;
use App\Manager\SettingsManager;
use App\Model\HostelSearch;
use App\Repository\CityRepository;
use App\Repository\HostelRepository;
use App\Storage\BookingStorage;
use Knp\Component\Pager\PaginatorInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $form->handleRequest($request);

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

        $data = array_merge(
            $this->hostelListDataHandler->retrieveData($requestData),
            $this->hostelSortDataHandler->retrieveData($requestData),
            $this->paginationDataHandler->retrieveData($requestData)
        );

        $hostels = $this->hostelFinder->find($data);

        return $this->render('site/hostel/index.html.twig', [
            'hostels' => $hostels,
            'city' => $data['city'],
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/hostels/{slug}', name: 'app_hostel_show')]
    public function show(string $slug)
    {
        $hostel = $this->repository->getBySlug($slug);
        $this->showBreadcrumbs($hostel);

        $this->dispatcher->dispatch(new HostelViewEvent($hostel));

        return $this->render('site/hostel/show.html.twig', [
            'settings' => $this->settings,
            'hostel' => $hostel,
        ]);
    }

    private function showBreadcrumbs(Hostel $hostel)
    {
        $this->breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'))
            ->addItem('Hôtels', $this->generateUrl('app_hostel_index'))
            ->addItem($hostel->getCategory()->getName(), $this->generateUrl('app_hostel_index', ['type' => $hostel->getCategory()->getName()]))
            //->addItem($hostel->getCity()->getName(), $this->generateUrl('app_hostel_index', ['city' => $hostel->getCity()->getName()]))
            ->addItem('Les offres de l\'établissement '. $hostel->getName());

        return $this->breadcrumbs;
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

/*$this->breadcrumbs($request);
        $this->dispatcher->dispatch(new HostelListingEvent($request));

        $search = new HostelSearch();
        $search = $this->hydrate($request, $search);

        $form = $this->createForm(HostelType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getTotalEnabled($search);
        $hostels = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 30);

        return $this->render('site/hostel/index.html.twig', [
            'settings' => $this->settings,
            'hostels' => $hostels,
            'city' => $this->cityRepository->getByName($request->query->get('location')),
            'form' => $form->createView()
        ]);*/

