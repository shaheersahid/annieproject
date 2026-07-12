<?php

namespace App\Models;

use App\Traits\AutoGeneratesSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttribute extends Model
{
    use HasFactory, SoftDeletes, AutoGeneratesSlug;

    public const INPUT_TYPES = [
        'dropdown' => 'Dropdown',
        'radio' => 'Radio Button',
        'checkbox' => 'Checkbox',
        'color_switch' => 'Color Switch',
        'textinput' => 'Text Input',
    ];

    protected $fillable = [
        'name',
        'slug',
        'value',
        'input_type',
        'short_description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product_attribute')->withTimestamps();
    }
}
