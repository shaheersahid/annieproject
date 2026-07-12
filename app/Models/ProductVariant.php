<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'sku',
        'attributes',
        'price',
        'sale_price',
        'deal_enabled',
        'deal_type',
        'deal_value',
        'deal_start_at',
        'deal_end_at',
        'stock',
        'low_stock_threshold',
        'sold_out',
        'is_active',
        'image_path',
        'position',
        'combination_hash',
    ];

    protected $casts = [
        'attributes' => 'array',
        'is_active' => 'boolean',
        'deal_enabled' => 'boolean',
        'deal_start_at' => 'datetime',
        'deal_end_at' => 'datetime',
        'low_stock_threshold' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventorySnapshots(): HasMany
    {
        return $this->hasMany(InventorySnapshot::class, 'variant_id');
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'variant_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    public function getStockQuantityAttribute(): mixed
    {
        return $this->stock;
    }
}
