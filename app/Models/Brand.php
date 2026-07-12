<?php
namespace App\Models;

use App\Traits\HasImages;
use App\Traits\AutoGeneratesSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory, AutoGeneratesSlug, HasImages;

    protected $fillable = ['name', 'slug', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

     /**
     * Scope a query to only include active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the brand URL.
     */
    public function getUrlAttribute(): string
    {
        return url('brands/' . $this->slug);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
