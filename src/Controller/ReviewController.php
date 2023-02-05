<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Data\ReviewData;
use App\Entity\Booking;
use App\Entity\Hostel;
use App\Event\ReviewCreateEvent;
use App\Form\ReviewDataType;
use App\Form\ReviewType;
use App\Manager\BookingManager;
use App\Manager\ReviewManager;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ReviewController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private ReviewManager $manager,
        private BookingManager $bookingManager,
        private EventDispatcherInterface $dispatcher,
        private ReviewRepository $repository
    )
    {
    }

    public function index(Hostel $hostel): Response
    {
        return $this->render('site/review/index.html.twig', [
            'reviews' => $this->repository->getByHostel($hostel),
            'hostel' => $hostel
        ]);
    }

    #[Route(path: '/reviews/{id}/create', name: 'app_review_create', requirements: ['id' => '\d+'])]
    public function create(Request $request, Breadcrumbs $breadcrumbs, Booking $booking): RedirectResponse|Response
    {
        $this->breadcrumb($breadcrumbs)
            ->addItem('Laissez un avis sur l\'établissement '. $booking->getHostel()->getName());

        $review = $this->manager->createNew($booking);

        $form = $this->createForm(ReviewType::class, $review, [
            'action' => $this->generateUrl('app_review_create', ['id' => $booking->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $review->setRating($this->manager->note($review));

            $this->repository->add($review, true);

            $this->dispatcher->dispatch(new ReviewCreateEvent($review));

            $this->addFlash('success', 'Merci pour votre commentaire');

            return $this->redirectToRoute('app_hostel_show', ['slug' => $booking->getHostel()->getSlug()]);
        }

        return $this->render('site/review/create.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reviews/{id}/check', name: 'app_review_check', requirements: ['id' => '\d+'])]
    public function check(Request $request, Hostel $hostel): RedirectResponse|Response
    {
        $data = new ReviewData();

        $form = $this->createForm(ReviewDataType::class, $data, [
            'action' => $this->generateUrl('app_review_check', ['id' => $hostel->getId()])
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($booking = $this->bookingManager->getBookingByReferenceAndHostel($hostel, $data->number)) {
                return $this->redirectToRoute('app_review_create', ['id' => $booking->getId()]);
            } else {
                $this->addFlash('error', 'Vous ne pouvez pas laisser un avis sur cet établissement.');

                return $this->redirectToRoute('app_hostel_show', ['slug' => $hostel->getSlug()]);
            }
        } else if ($form->isSubmitted()) {
            $this->addFlash('error', 'Veuillez entrer un numéro de réservation.');

            return $this->redirectToRoute('app_hostel_show', ['slug' => $hostel->getSlug()]);
        }

        return $this->render('site/review/data.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

