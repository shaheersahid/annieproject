<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\AutoGeneratesSlug;

class ProductTag extends Model
{
    use HasFactory, AutoGeneratesSlug;

    public const TYPES = [
        'product' => 'Product',
        'category' => 'Category',
        'brand' => 'Brand',
        'collection' => 'Collection',
    ];

    public const OPTIONS = [
        'featured' => 'Featured',
        'trending' => 'Trending',
        'new_arrival' => 'New Arrival',
        'on_sale' => 'On Sale',
        'bestseller' => 'Bestseller',
    ];

    protected $fillable = ['name', 'slug', 'type', 'option', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag_product');
    }
}
