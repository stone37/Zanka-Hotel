<?php

namespace App\QueryBuilder;

use App\Context\StorageBasedCurrencyContext;
use App\Converter\CurrencyConverter;
use App\Entity\Settings;
use App\Form\DataTransformer\MoneyTransformer;
use App\Manager\SettingsManager;
use App\PropertyNameResolver\ConcatedPriceNameResolver;
use App\PropertyNameResolver\PriceNameResolver;
use Elastica\Query\AbstractQuery;
use Elastica\Query\Nested;
use Elastica\Query\Range;
use NumberFormatter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HasPriceBetweenQueryBuilder
{
    private PriceNameResolver $priceNameResolver;
    private ConcatedPriceNameResolver $concatedPriceNameResolver;
    private StorageBasedCurrencyContext $currencyContext;
    private CurrencyConverter $currencyConverter;
    private ?Settings $settings;
    private ?string $roomsProperty;

    public function __construct(
        PriceNameResolver $priceNameResolver,
        ConcatedPriceNameResolver $concatedPriceNameResolver,
        StorageBasedCurrencyContext $currencyContext,
        CurrencyConverter $currencyConverter,
        SettingsManager $settingsManager,
        ParameterBagInterface $parameterBag
    )
    {
        $this->priceNameResolver = $priceNameResolver;
        $this->concatedPriceNameResolver = $concatedPriceNameResolver;
        $this->currencyContext = $currencyContext;
        $this->currencyConverter = $currencyConverter;
        $this->settings = $settingsManager->get();
        $this->roomsProperty = $parameterBag->get('app_room_property_prefix');
    }

    public function buildQuery(array $data): ?AbstractQuery
    {
        $dataMinPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMinPriceName());
        $dataMaxPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMaxPriceName());

        $minPrice = $dataMinPrice ? $this->resolveBasePrice($dataMinPrice) : null;
        $maxPrice = $dataMaxPrice ? $this->resolveBasePrice($dataMaxPrice) : null;

        $propertyName = $this->concatedPriceNameResolver->resolvePropertyName('price');
        $rangeQuery = new Range();

        $paramValue = $this->getQueryParamValue($minPrice, $maxPrice);

        if (null === $paramValue) {
            return null;
        }

        $rangeQuery->setParam($propertyName, $paramValue);

        $priceDomainQuery = new Nested();
        $priceDomainQuery->setQuery($rangeQuery)->setPath($this->roomsProperty);

        return $priceDomainQuery;
    }

    private function resolveBasePrice(string $price): int
    {
        $price = $this->convertFromString($price);

        $currentCurrencyCode = $this->currencyContext->getCurrencyCode($this->settings);
        $baseCurrencyCode = $this->settings->getBaseCurrency()->getCode();

        if ($currentCurrencyCode !== $baseCurrencyCode) {
            $price = $this->currencyConverter->convert($price, $currentCurrencyCode, $baseCurrencyCode);
        }

        return $price;
    }

    private function convertFromString(string $price): int
    {
        $transformer = new MoneyTransformer(2, false, NumberFormatter::ROUND_HALFUP, 100);

        return $transformer->reverseTransform($price);
    }

    private function getDataByKey(array $data, ?string $key = null): ?string
    {
        return $data[$key] ?? null;
    }

    private function getQueryParamValue(?int $min, ?int $max): ?array
    {
        foreach (['gte' => $min, 'lte' => $max] as $key => $value) {
            if (null !== $value) {
                $paramValue[$key] = $value;
            }
        }

        return $paramValue ?? null;
    }
}
