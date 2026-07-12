<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Image;
use App\Models\Product;
use App\Services\FrontendCacheService;
use App\Traits\AutoGeneratesSlug;
use App\Traits\HasSeo;

class Category extends Model
{
    use HasFactory, AutoGeneratesSlug, HasSeo;

    protected static function booted(): void
    {
        static::saved(fn () => app(FrontendCacheService::class)->clearCategories());
        static::deleted(fn () => app(FrontendCacheService::class)->clearCategories());
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'sort_order',
        'is_active',
        'show_on_home',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_home' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationships
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getImageAttribute(): ?string
    {
        return $this->images?->path;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->images?->url;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getAllChildIds()
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildIds());
        }
        return $ids;
    }

    public function getTotalProductsCountAttribute()
    {
        $storeId = session('selected_store_id');
        $childIds = $this->getAllChildIds();
        
        $query = Product::whereHas('categories', function($q) use ($childIds) {
            $q->whereIn('categories.id', $childIds);
        })->active();

        if ($storeId) {
            $query->inStock($storeId);
        }
        
        return $query->count();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getUrlAttribute(): string
    {
        $path = [$this->slug];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->slug);
            $parent = $parent->parent;
        }
        
        return url(implode('/', $path));
    }
}
