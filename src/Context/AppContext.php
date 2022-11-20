<?php

namespace App\Context;

use App\Entity\Settings;
use App\Entity\User;
use App\Manager\SettingsManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AppContext
{
    private ?Settings $settings;
    private StorageBasedCurrencyContext $currencyContext;
    private StorageBasedLocaleContext $localeContext;
    private Security $security;

    public function __construct(
        SettingsManager $manager,
        StorageBasedCurrencyContext $currencyContext,
        StorageBasedLocaleContext $localeContext,
        Security $security
    )
    {
        $this->settings = $manager->get();
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
        $this->security = $security;
    }

    public function getSettings(): ?Settings
    {
        return $this->settings;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyContext->getCurrencyCode($this->settings);
    }

    public function getLocaleCode(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * @return User|UserInterface
     */
    public function getUser(): ?User
    {
        return $this->security->getUser();
    }
}