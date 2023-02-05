<?php

namespace App\Controller;

use Symfony\Component\Notifier\Message\SmsMessage;
use App\Controller\Traits\ControllerTrait;
use App\Data\BookingCancelledData;
use App\Data\UserBookingCancelledData;
use App\Entity\Booking;
use App\Event\BookingCancelledEvent;
use App\Form\BookingCancelledType;
use App\Form\UserBookingCancelledType;
use App\Manager\BookingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserBookingCancelController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private BookingManager $manager,
        private EventDispatcherInterface $dispatcher,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    #[Route(path: '/bookings/{id}/cancelled', name: 'app_booking_cancelled', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function index(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $sms = new SmsMessage(

        );
        $data = new BookingCancelledData();

        $form = $this->createForm(BookingCancelledType::class, $data, [
            'action' => $this->generateUrl('app_booking_cancelled', ['id' => $booking->getId()])
        ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($booking->getEmail() != $data->getEmail()) {
                    $this->addFlash('error', 'Impossible d\'annuler la réservation, adresse mail invalide');

                    return new RedirectResponse($request->request->get('referer'));
                }

                $this->manager->cancel($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));

                $this->addFlash('success', 'La réservation a été annuler avec succès');
            } else {
                $this->addFlash('error', 'Désolé, la réservation n\'a pas pu être annulée');
            }

            return new RedirectResponse($request->request->get('referer'));
        }

        $render = $this->render('site/booking/modal/_cancelled.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/u/bookings/{id}/cancelled', name: 'app_user_booking_cancelled', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function byUser(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $user = $this->getUserOrThrow();

        $data = new UserBookingCancelledData();

        $form = $this->createForm(UserBookingCancelledType::class, $data, [
            'action' => $this->generateUrl('app_user_booking_cancelled', ['id' => $booking->getId()])
        ]);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if (!$this->passwordHasher->isPasswordValid($user, $data->getPassword() ?? '')) {
                    $this->addFlash('error', 'Impossible d\'annuler la réservation, mot de passe invalide');

                    return new RedirectResponse($request->request->get('referer'));
                }

                $this->manager->cancel($booking);

                $this->dispatcher->dispatch(new BookingCancelledEvent($booking));

                $this->addFlash('success', 'La réservation a été annuler');
            } else {
                $this->addFlash('error', 'Désolé, la réservation n\'a pas été annulée');
            }

            return new RedirectResponse($request->request->get('referer'));
        }

        $render = $this->render('site/booking/modal/_user_cancelled.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}
