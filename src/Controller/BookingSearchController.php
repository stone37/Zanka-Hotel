<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\BookingSearchRequestData;
use App\Data\BookingSearchVerifyRequestData;
use App\Form\BookingSearchRequestForm;
use App\Form\BookingSearchVerifyRequestForm;
use App\Repository\BookingRepository;
use App\Repository\BookingSearchTokenRepository;
use App\Service\BookerService;
use App\Storage\SessionStorage;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class BookingSearchController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private Security $security,
        private BookerService $booker,
        private BookingSearchTokenRepository $repository,
        private BookingRepository $bookingRepository,
        private SessionStorage $storage,
        private Breadcrumbs $breadcrumbs,
        private PaginatorInterface $paginator
    )
    {
    }

    #[Route(path: '/booking/search', name: 'app_booking_search')]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->myNotFoundException();

        $this->breadcrumb($this->breadcrumbs)->addItem('Rechercher vos reservations');

        $data = new BookingSearchRequestData();

        $form = $this->createForm(BookingSearchRequestForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->getSession()->has($this->provideKey())) {
                $email = $request->getSession()->get($this->provideKey());

                if ($email === $data->email) {
                    return $this->redirectToRoute('app_booking_search_show');
                }
            }

            $this->booker->searchBooking($form->getData());

            $this->addFlash('success', 'Un code de vérification vous a été envoyé par mail');

            return $this->redirectToRoute('app_booking_search_verify');
        }

        return $this->render('site/booking/search.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/booking/search/verify', name: 'app_booking_search_verify')]
    public function verify(Request $request): RedirectResponse|Response
    {
        $this->myNotFoundException();

        $this->breadcrumb($this->breadcrumbs)->addItem('Rechercher vos reservations');

        $data = new BookingSearchVerifyRequestData();

        $form = $this->createForm(BookingSearchVerifyRequestForm::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $this->repository->findOneBy(['code' => $data->getCode()]);

            if (!$token || $this->booker->isExpired($token)) {
                $this->addFlash('error', 'Ce code a expiré');

                return $this->redirectToRoute('app_booking_search');
            }

            $request->getSession()->set($this->provideKey(), $token->getEmail());

            $this->repository->clean();

            return $this->redirectToRoute('app_booking_search_show');
        }

        return $this->render('site/booking/verify.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/booking/search/show', name: 'app_booking_search_show')]
    public function show(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)
            ->addItem('Rechercher', $this->generateUrl('app_booking_search'))
            ->addItem('Tous vos réservations');
        
        $this->myNotFoundException();

        if (!$request->getSession()->has($this->provideKey())) {
            $this->createNotFoundException();
        }

        $email = $request->getSession()->get($this->provideKey());
        $bookings = $this->bookingRepository->getByEmail($email);
        $bookings = $this->paginator->paginate($bookings, $request->query->getInt('page', 1), 20);

        return $this->render('site/booking/show.html.twig', ['bookings' => $bookings]);
    }

    private function myNotFoundException(string $message = 'Not Found'): ?NotFoundHttpException
    {
        return $this->security->getUser() ? $this->createNotFoundException($message): null;
    }

    private function provideKey(): string
    {
        return '_app_booking_search';
    }
}
