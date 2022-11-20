<?php

namespace App\Storage;

use App\Entity\Settings;
use App\Exception\LocaleNotFoundException;
use App\Manager\SettingsManager;

class LocaleStorage
{
    private CookieStorage $storage;
    private ?Settings $settings;

    public function __construct(CookieStorage $storage, SettingsManager $settings)
    {
        $this->storage = $storage;
        $this->settings = $settings->get();
    }

    public function set(string $localeCode): void
    {
        $this->storage->set($this->provideKey(), $localeCode);
    }

    public function get(): string
    {
        $localeCode = $this->storage->get($this->provideKey());

        if (null === $localeCode) {
            $localeCode = $this->settings->getDefaultLocale()->getCode();

            if (null === $localeCode) {
                throw new LocaleNotFoundException('No locale is set for current channel !');
            }
        }

        return $localeCode;
    }

    private function provideKey(): string
    {
        return '_app_locale';
    }
}
