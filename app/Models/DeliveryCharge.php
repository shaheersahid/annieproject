<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_city',
        'to_city',
        'charge',
        'is_active',
    ];

    protected $casts = [
        'charge' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function resolveCharge(string $fromCity, string $toCity, float $subtotal = 0, int $itemQuantity = 0): float
    {
        if (self::qualifiesForFreeDelivery($subtotal, $itemQuantity)) {
            return 0.0;
        }

        $charge = static::query()
            ->where('is_active', true)
            ->whereRaw('LOWER(from_city) = ?', [strtolower(trim($fromCity))])
            ->whereRaw('LOWER(to_city) = ?', [strtolower(trim($toCity))])
            ->value('charge');

        if ($charge !== null) {
            return (float) $charge;
        }

        return (float) Setting::get('delivery.default_charge', 0);
    }

    public static function qualifiesForFreeDelivery(float $subtotal = 0, int $itemQuantity = 0): bool
    {
        $enabled = (bool) Setting::get('delivery.free_delivery_enabled', false);

        if (! $enabled) {
            return false;
        }

        $minimumOrderAmount = (float) Setting::get('delivery.free_delivery_min_order_amount', 0);

        if ($minimumOrderAmount > 0 && $subtotal >= $minimumOrderAmount) {
            return true;
        }

        $minimumItemQuantity = (int) Setting::get('delivery.free_delivery_min_item_quantity', 0);

        return $minimumItemQuantity > 0 && $itemQuantity >= $minimumItemQuantity;
    }
}
