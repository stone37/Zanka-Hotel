<?php

namespace App\Util;

use App\Converter\CurrencyConverter;

class ConvertMoneyUtil
{
    private CurrencyConverter $converter;

    public function __construct(CurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    public function convertAmount(int $amount, ?string $sourceCurrencyCode, ?string $targetCurrencyCode): string
    {
        return (string) $this->converter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }
}

