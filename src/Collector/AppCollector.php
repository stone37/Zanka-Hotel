<?php

namespace App\Collector;

use App\Context\AppContext;
use App\Entity\Settings;
//use App\Exception\CurrencyNotFoundException;
//use App\Exception\LocaleNotFoundException;
use App\Storage\BookingStorage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppCollector
{
    private AppContext $appContext;
    private BookingStorage $storage;
    private ParameterBagInterface $parameter;
    //private array $data;

    public function __construct(
        AppContext $appContext,
        BookingStorage $storage,
        ParameterBagInterface $parameter
    )
    {
        $this->appContext = $appContext;
        $this->storage = $storage;
        $this->parameter = $parameter;

        /*$this->data = [
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => $parameter->get('locale'),
            'locale_code' => null,
            'name' => null,
        ];

        $this->collect();*/
    }

    public function getName(): string
    {
        return $this->getSettings()->getName();
    }

    public function getDefaultLocaleCode(): ?string
    {
        return $this->parameter->get('locale');
    }

    public function getLocaleCode(): ?string
    {
        return $this->appContext->getLocaleCode();
    }

    public function getCurrencyCode(): ?string
    {
        return $this->appContext->getCurrencyCode();
    }

    public function getDefaultCurrencyCode(): ?string
    {
        return $this->getSettings()->getBaseCurrency()->getCode();
    }

    public function getSettings(): ?Settings
    {
        return $this->appContext->getSettings();
    }

    public function getBooker(): ?BookingStorage
    {
        return $this->storage;
    }

    /*private function collect()
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
    }*/
}
