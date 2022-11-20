<?php

namespace App\Context;

use App\Exception\LocaleNotFoundException;
use App\Provider\LocaleProvider;
use App\Storage\LocaleStorage;

class StorageBasedLocaleContext
{
    private LocaleStorage $localeStorage;
    private LocaleProvider $localeProvider;

    public function __construct(
        LocaleStorage $localeStorage,
        LocaleProvider $localeProvider
    )
    {
        $this->localeStorage = $localeStorage;
        $this->localeProvider = $localeProvider;
    }

    public function getLocaleCode(): string
    {
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();

        try {
            $localeCode = $this->localeStorage->get();
        } catch (LocaleNotFoundException) {}

        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}