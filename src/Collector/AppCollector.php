<?php

namespace App\Collector;

use App\Context\AppContext;
use App\Entity\Settings;
use App\Exception\CurrencyNotFoundException;
use App\Exception\LocaleNotFoundException;
use App\Storage\BookingStorage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppCollector
{
    private AppContext $appContext;
    private BookingStorage $storage;
    private array $data;

    public function __construct(
        AppContext $appContext,
        BookingStorage $storage,
        ParameterBagInterface $parameter
    )
    {
        $this->appContext = $appContext;
        $this->storage = $storage;

        $this->data = [
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => $parameter->get('locale'),
            'locale_code' => null,
            'name' => null,
        ];

        $this->collect();
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getDefaultLocaleCode(): ?string
    {
        return $this->data['default_locale_code'];
    }

    public function getLocaleCode(): ?string
    {
        return $this->data['locale_code'];
    }

    public function getCurrencyCode(): ?string
    {
        return $this->data['currency_code'];
    }

    public function getDefaultCurrencyCode(): ?string
    {
        return $this->data['base_currency_code'];
    }

    public function getSettings(): ?Settings
    {
        return $this->appContext->getSettings();
    }

    public function getBooker(): ?BookingStorage
    {
        return $this->storage;
    }

    private function collect()
    {
        $settings = $this->appContext->getSettings();

        $this->data['name'] =  $settings->getName();

        try {
            $this->data['base_currency_code'] = $settings->getBaseCurrency()->getCode();
            $this->data['currency_code'] = $this->appContext->getCurrencyCode();
        } catch (CurrencyNotFoundException) {}

        try {
            $this->data['locale_code'] = $this->appContext->getLocaleCode();
        } catch (LocaleNotFoundException) {}
    }

    public function reset(): void
    {
        $this->data['base_currency_code'] = null;
        $this->data['currency_code'] = null;
        $this->data['locale_code'] = null;
        $this->data['name'] = null;
    }
}
