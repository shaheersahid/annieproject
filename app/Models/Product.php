<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\AutoGeneratesSlug;
use App\Traits\HasImages;
use App\Traits\HasSeo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes, AutoGeneratesSlug, HasImages, HasSeo;

    protected $fillable = [
        'brand_id',
        'seller_id',
        'size_chart_id',
        'name',
        'slug',
        'sku',
        'product_type',
        'affiliate_platform',
        'amazon_url',
        'temu_url',
        'external_product_id',
        'has_variants',
        'status',
        'review_status',
        'base_price',
        'sale_price',
        'price_note',
        'affiliate_rating',
        'deal_enabled',
        'deal_type',
        'deal_value',
        'deal_start_at',
        'deal_end_at',
        'stock',
        'low_stock_threshold',
        'sold_out',
        'short_description',
        'description',
        'specifications',
        'pros',
        'cons',
        'video_path',
        'is_draft',
        'is_active',
        'is_featured',
        'click_count',
        'out_of_stock',
    ];

    protected $casts = [
        'has_variants' => 'boolean',
        'seo_meta' => 'array',
        'deal_enabled' => 'boolean',
        'deal_start_at' => 'datetime',
        'deal_end_at' => 'datetime',
        'is_draft' => 'boolean',
        'is_active' => 'boolean',
        'out_of_stock' => 'boolean',
        'specifications' => 'array',
        'pros' => 'array',
        'cons' => 'array',
        'is_featured' => 'boolean',
        'click_count' => 'integer',
        'affiliate_rating' => 'decimal:2',
        'low_stock_threshold' => 'integer',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag_product');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inventorySnapshots(): HasMany
    {
        return $this->hasMany(InventorySnapshot::class);
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class);
    }

    public function affiliateClicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function getIsAffiliateAttribute(): bool
    {
        return in_array($this->affiliate_platform, ['amazon', 'temu', 'both'], true)
            && (filled($this->amazon_url) || filled($this->temu_url));
    }

    public function getAffiliatePlatformsAttribute(): array
    {
        return collect([
            'amazon' => $this->amazon_url,
            'temu' => $this->temu_url,
        ])->filter(fn ($url) => filled($url))->keys()->all();
    }

    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class);
    }

    public function getPriceAttribute(): mixed
    {
        return $this->base_price;
    }

    public function getTypeAttribute(): mixed
    {
        return $this->product_type;
    }

    public function scopeDraft($query)
    {
        return $query->where('is_draft', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_draft', false)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query, $storeId = null)
    {
        return $query->where('stock', '>', 0)->where('out_of_stock', false);
    }

    public function getStockQuantityAttribute(): mixed
    {
        return $this->stock;
    }
}
