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
use Illuminate\Support\Facades\Schema;

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
        'aliexpress_url',
        'tiktok_url',
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
        'is_latest',
        'is_reel',
        'latest_sort_order',
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
        'is_latest' => 'boolean',
        'is_reel' => 'boolean',
        'latest_sort_order' => 'integer',
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
        return in_array($this->affiliate_platform, ['amazon', 'temu', 'aliexpress', 'both', 'all'], true)
            && (filled($this->amazon_url) || filled($this->temu_url) || filled($this->aliexpress_url));
    }

    public function getAffiliatePlatformsAttribute(): array
    {
        return collect([
            'amazon' => $this->amazon_url,
            'temu' => $this->temu_url,
            'aliexpress' => $this->aliexpress_url,
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

    public function scopeWithListing($query)
    {
        return $query->with(['categories', 'images', 'primaryImage', 'brand', 'tags']);
    }

    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function ($search) use ($term): void {
            $like = '%' . $term . '%';

            $search->where('name', 'like', $like)
                ->orWhere('short_description', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('sku', 'like', $like)
                ->orWhere('price_note', 'like', $like)
                ->orWhereHas('brand', function ($brand) use ($like): void {
                    $brand->where('name', 'like', $like);
                })
                ->orWhereHas('tags', function ($tag) use ($like): void {
                    $tag->where('name', 'like', $like)->where('is_active', true);
                })
                ->orWhereHas('categories', function ($category) use ($like): void {
                    $category->where('name', 'like', $like);
                });
        });
    }

    public function scopeForPlatform($query, ?string $platform)
    {
        $platform = trim((string) $platform);

        if (! in_array($platform, ['amazon', 'temu', 'aliexpress'], true)) {
            return $query;
        }

        $urlColumn = $platform . '_url';

        return $query->whereNotNull($urlColumn)->where($urlColumn, '!=', '');
    }

    public function scopeLatestPicks($query)
    {
        if (! Schema::hasColumn($this->getTable(), 'is_latest')) {
            return $query->latest();
        }

        $query->where('is_latest', true);

        if (Schema::hasColumn($this->getTable(), 'is_reel')) {
            $query->orderByDesc('is_reel');
        }

        if (Schema::hasColumn($this->getTable(), 'tiktok_url')) {
            $query->orderByRaw('CASE WHEN tiktok_url IS NULL OR tiktok_url = "" THEN 1 ELSE 0 END');
        }

        return $query->orderBy('latest_sort_order')->orderByDesc('updated_at');
    }

    public function isTiktokReel(): bool
    {
        return (bool) $this->is_reel || filled($this->tiktok_url);
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

    public function enabledTags()
    {
        $tags = $this->relationLoaded('tags')
            ? $this->tags
            : $this->tags()->get();

        return $tags->where('is_active', true)->values();
    }

    public function enabledAttributes()
    {
        $categoryIds = $this->relationLoaded('categories')
            ? $this->categories->pluck('id')
            : $this->categories()->pluck('categories.id');

        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return ProductAttribute::query()
            ->where('is_active', true)
            ->whereHas('categories', function ($query) use ($categoryIds): void {
                $query->whereIn('categories.id', $categoryIds);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getSeoUrl(): string
    {
        return route('product-detail', $this);
    }

    public function getSeoRobots(): string
    {
        if ($this->is_draft || ! $this->is_active) {
            return 'noindex, nofollow';
        }

        return (string) ($this->seoField('meta_fields', 'robots') ?: 'index, follow');
    }

    public function resolvedSchema(): array
    {
        $custom = $this->seo?->schema_fields;

        if (is_array($custom) && (isset($custom['@context']) || isset($custom['@type']) || isset($custom['@graph']))) {
            if (! isset($custom['@context'])) {
                $custom = ['@context' => 'https://schema.org'] + $custom;
            }

            return $custom;
        }

        return $this->defaultProductSchema();
    }

    public function defaultProductSchema(): array
    {
        $url = $this->getSeoUrl();
        $images = collect([$this->primaryImage?->url])
            ->merge($this->images?->pluck('url') ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();

        $offers = [];
        foreach (['amazon' => 'Amazon', 'temu' => 'Temu', 'aliexpress' => 'AliExpress'] as $platform => $seller) {
            if (! filled($this->{$platform . '_url'})) {
                continue;
            }

            $offer = [
                '@type' => 'Offer',
                'url' => $this->{$platform . '_url'},
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $seller,
                ],
                'availability' => 'https://schema.org/InStock',
            ];

            $price = (float) ($this->sale_price ?: $this->base_price);
            if ($price > 0) {
                $offer['price'] = number_format($price, 2, '.', '');
                $offer['priceCurrency'] = 'USD';
            }

            $offers[] = $offer;
        }

        if ($offers === []) {
            $offers[] = [
                '@type' => 'Offer',
                'url' => $url,
                'availability' => 'https://schema.org/InStock',
            ];
        }

        $product = [
            '@type' => 'Product',
            '@id' => $url . '#product',
            'name' => $this->getSeoTitle(),
            'description' => $this->getSeoDescription() ?: $this->name,
            'image' => $images ?: [$this->getSeoImage()],
            'url' => $url,
            'offers' => count($offers) === 1 ? $offers[0] : $offers,
        ];

        if (filled($this->sku)) {
            $product['sku'] = $this->sku;
        }

        if ($this->brand) {
            $product['brand'] = [
                '@type' => 'Brand',
                'name' => $this->brand->name,
            ];
        }

        if ($this->categories?->isNotEmpty()) {
            $product['category'] = $this->categories->pluck('name')->implode(', ');
        }

        $breadcrumbs = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => route('home'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Deals',
                'item' => route('product-list'),
            ],
        ];

        $category = $this->categories?->first();
        if ($category) {
            $breadcrumbs[] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $category->name,
                'item' => route('product-list', ['category' => $category->slug]),
            ];
        }

        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => count($breadcrumbs) + 1,
            'name' => $this->name,
            'item' => $url,
        ];

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                $product,
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => $breadcrumbs,
                ],
                [
                    '@type' => 'WebPage',
                    '@id' => $url . '#webpage',
                    'url' => $url,
                    'name' => $this->getSeoTitle(),
                    'description' => $this->getSeoDescription(),
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => 'Smart Comfort Deals',
                        'url' => route('home'),
                    ],
                ],
            ],
        ];
    }
}
