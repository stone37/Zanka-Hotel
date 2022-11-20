<?php

namespace App\Controller\RequestDataHandler;

use App\PropertyNameResolver\ConcatedPriceNameResolver;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use UnexpectedValueException;

class HostelSortDataHandler
{
    public const ORDER_BY_INDEX = 'order_by';
    public const SORT_INDEX = 'sort';
    public const SORT_ASC_INDEX = 'asc';
    public const SORT_DESC_INDEX = 'desc';

    private ConcatedPriceNameResolver $concatedPriceNameResolver;
    private ?string $positionPropertyPrefix;
    private ?string $createdAtProperty;
    //private ?string $pricePropertyPrefix;

    public function __construct(
        ConcatedPriceNameResolver $concatedPriceNameResolver,
        ParameterBagInterface $parameterBag
    )
    {
        $this->concatedPriceNameResolver = $concatedPriceNameResolver;
        $this->positionPropertyPrefix = $parameterBag->get('app_position_property_prefix');
        $this->createdAtProperty = $parameterBag->get('app_created_at_property_prefix');
        //$this->pricePropertyPrefix = $parameterBag->get('app');
    }

    public function retrieveData(array $requestData): array
    {
        $data = [];
        $positionSortingProperty = $this->positionPropertyPrefix;

        $orderBy = $requestData[self::ORDER_BY_INDEX] ?? $positionSortingProperty;
        $sort = $requestData[self::SORT_INDEX] ?? self::SORT_ASC_INDEX;

        // $this->soldUnitsProperty,
        $availableSorters = [$positionSortingProperty, $this->createdAtProperty, $this->concatedPriceNameResolver->resolvePropertyName('price')];
        $availableSorting = [self::SORT_ASC_INDEX, self::SORT_DESC_INDEX];

        if (!in_array($orderBy, $availableSorters) || !in_array($sort, $availableSorting)) {
            throw new UnexpectedValueException();
        }

        /*if ($this->pricePropertyPrefix === $orderBy) {
            $channelCode = $this->channelContext->getChannel()->getCode();
            $orderBy = $this->channelPricingNameResolver->resolvePropertyName($channelCode);
        }*/

        $data['sort'] = [$orderBy => ['order' => strtolower($sort), 'unmapped_type' => 'keyword']];

        return $data;
    }
}