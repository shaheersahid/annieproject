<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Calculate effective price considering active promotions or discounts.
     */
    public function getEffectivePrice($model)
    {
        // Sale price overrides discount calculations when present.
        if ($model->sale_price) {
            return $model->sale_price;
        }

        if ($model->discount_value && $model->discount_type) {
            if ($model->discount_type === 'fixed') {
                return max(0, $model->price - $model->discount_value);
            }
            if ($model->discount_type === 'percentage') {
                return max(0, $model->price - ($model->price * ($model->discount_value / 100)));
            }
        }

        return $model->price;
    }

    /**
     * Adjust stock for a trackable entity (Product or ProductVariant) and log movement.
     */
    public function adjustStock($trackable, int $quantity, string $type = 'adjustment', string $notes = null, $userId = null)
    {
        return DB::transaction(function () use ($trackable, $quantity, $type, $notes, $userId) {
            // Adjust the actual stock quantity on the model
            if ($type === 'sale') {
                $trackable->decrement('stock', abs($quantity));
            } else if ($type === 'purchase' || $type === 'return') {
                $trackable->increment('stock', abs($quantity));
            } else {
                // Manual adjustments can be positive or negative
                $trackable->stock += $quantity;
                $trackable->save();
            }

            // Record the movement for history and analytics
            return InventoryMovement::create([
                'trackable_id' => $trackable->id,
                'trackable_type' => get_class($trackable),
                'type' => $type,
                'quantity' => $quantity,
                'notes' => $notes,
                'user_id' => $userId,
            ]);
        });
    }

    /**
     * Retrieve products and variants that are at or below their low stock threshold.
     */
    public function getLowStockAlerts()
    {
        $simpleProductsLow = Product::where('has_variants', false)
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->get();

        $variantsLow = ProductVariant::whereColumn('stock', '<=', 'low_stock_threshold')
            ->with('product')
            ->get();

        return [
            'products' => $simpleProductsLow,
            'variants' => $variantsLow
        ];
    }
}
