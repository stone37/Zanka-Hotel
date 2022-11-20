<?php

namespace App\EventListener;

use App\Context\ImmutableLocaleContext;
use App\Provider\LocaleProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestLocaleSetter implements EventSubscriberInterface
{
    private LocaleProvider $localeProvider;
    private ImmutableLocaleContext $localeContext;

    public function __construct(LocaleProvider $localeProvider, ImmutableLocaleContext $localeContext)
    {
        $this->localeProvider = $localeProvider;
        $this->localeContext = $localeContext;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $request->setLocale($this->localeContext->getLocaleCode());
        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());
    }
}