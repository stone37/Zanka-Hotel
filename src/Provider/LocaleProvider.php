<?php

namespace App\Provider;

use App\Entity\Locale;
use App\Repository\LocaleRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LocaleProvider
{
    private LocaleRepository $localeRepository;
    private ?string $defaultLocaleCode;

    public function __construct(LocaleRepository $localeRepository, ParameterBagInterface $parameter)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $parameter->get('locale');
    }

    public function getAvailableLocalesCodes(): array
    {
        $locales = $this->localeRepository->findAll();

        return array_map(
            function (Locale $locale) {return (string) $locale->getCode();},
            $locales
        );
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}