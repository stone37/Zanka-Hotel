<?php

namespace App\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class PartnerRequestSubscriber implements EventSubscriberInterface
{
    private const ROLE = 'ROLE_PARTNER';

    public function __construct(
        private RouterInterface $router,
        private Security $security
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::REQUEST => 'onPartnerRequest'];
    }

    public function onPartnerRequest(RequestEvent $event): void
    {
        if ($this->security->getUser() && in_array(self::ROLE, $this->security->getUser()->getRoles()))
        {
            $route = $event->getRequest()->get('_route');

            if (str_contains($route, 'notification')) {
                return;
            }

            if (str_contains($route, 'upload')) {
                return;
            }

            if (!str_contains($route, 'partner') && $event->isMainRequest()) {
                $response = new RedirectResponse($this->router->generate('app_partner_index'));
                $event->setResponse($response);
            }
        }
    }
}

