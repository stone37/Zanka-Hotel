<?php

namespace App\PropertyNameResolver;

class PriceNameResolver
{
    private string $pricePropertyPrefix;

    public function __construct(string $pricePropertyPrefix)
    {
        $this->pricePropertyPrefix = $pricePropertyPrefix;
    }

    public function resolveMinPriceName(): string
    {
        return 'min_' . $this->pricePropertyPrefix;
    }

    public function resolveMaxPriceName(): string
    {
        return 'max_' . $this->pricePropertyPrefix;
    }
}