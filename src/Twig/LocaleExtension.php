<?php

namespace App\Twig;

use App\Util\LocaleUtil;
use Locale;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LocaleExtension extends AbstractExtension
{
    private LocaleUtil $util;

    public function __construct(LocaleUtil $util)
    {
        $this->util = $util;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('app_locale_name', [$this->util, 'convertCodeToName']),
            new TwigFilter('app_locale_country', [$this, 'getCountryCode'])
        ];
    }

    public function getCountryCode(string $locale): ?string
    {
        return Locale::getRegion($locale);
    }
}