<?php

namespace App\Util;

use App\Context\ImmutableLocaleContext;
use App\Converter\LocaleConverter;
use App\Exception\LocaleNotFoundException;
use InvalidArgumentException;

class LocaleUtil
{
    private LocaleConverter $localeConverter;
    private ?ImmutableLocaleContext $localeContext;

    public function __construct(LocaleConverter $localeConverter, ImmutableLocaleContext $localeContext)
    {
         $this->localeConverter = $localeConverter;
         $this->localeContext = $localeContext;
    }

    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        try {
            return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (InvalidArgumentException) {
            return $code;
        }
    }

    private function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        if (null === $this->localeContext) {
            return null;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            return null;
        }
    }
}