<?php

namespace App\Services;

use App\Contracts\DealCalculationServiceInterface;
use App\Contracts\ProductPriceServiceInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;

class ProductPriceService implements ProductPriceServiceInterface
{
    public function __construct(
        private readonly DealCalculationServiceInterface $dealCalculationService
    ) {
    }

    public function resolve(Product $product, ?ProductVariant $variant = null, ?Carbon $at = null): array
    {
        $basePrice = $this->basePrice($product, $variant);
        $finalPrice = $basePrice;
        $dealApplied = false;
        $source = $variant ? 'variant' : 'product';

        if ($variant && $variant->deal_enabled) {
            $dealPrice = $this->dealCalculationService->applyDeal(
                amount: $basePrice,
                dealType: $variant->deal_type,
                dealValue: $variant->deal_value,
                startAt: $variant->deal_start_at,
                endAt: $variant->deal_end_at,
                at: $at
            );
            $dealApplied = $dealPrice !== $basePrice;
            $finalPrice = $dealPrice;
        } elseif ($product->deal_enabled) {
            $dealPrice = $this->dealCalculationService->applyDeal(
                amount: $basePrice,
                dealType: $product->deal_type,
                dealValue: $product->deal_value,
                startAt: $product->deal_start_at,
                endAt: $product->deal_end_at,
                at: $at
            );
            $dealApplied = $dealPrice !== $basePrice;
            $finalPrice = $dealPrice;
        }

        return [
            'source' => $source,
            'base_price' => $basePrice,
            'final_price' => $finalPrice,
            'deal_applied' => $dealApplied,
        ];
    }

    public function basePrice(Product $product, ?ProductVariant $variant = null): float
    {
        if ($variant && $variant->price !== null) {
            return (float) $variant->price;
        }

        return (float) $product->price;
    }

    public function finalPrice(Product $product, ?ProductVariant $variant = null, ?Carbon $at = null): float
    {
        return (float) $this->resolve($product, $variant, $at)['final_price'];
    }
}
