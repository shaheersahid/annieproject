<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class FrontendCacheService
{
    const CATEGORIES_KEY = 'frontend.categories.nav';
    const BRANDS_KEY     = 'frontend.brands.nav';
    const CACHE_TTL      = 900; // 15 minutes

    public function getNavCategories()
    {
        return Cache::remember(self::CATEGORIES_KEY, self::CACHE_TTL, fn () =>
            Category::active()
                ->parentCategories()
                ->with(['children' => fn ($q) => $q->active()->ordered()])
                ->ordered()
                ->get()
        );
    }

    public function getNavBrands()
    {
        return Cache::remember(self::BRANDS_KEY, self::CACHE_TTL, fn () =>
            Brand::active()->orderBy('name')->get()
        );
    }

    public function clearCategories(): void
    {
        Cache::forget(self::CATEGORIES_KEY);
    }

    public function clearBrands(): void
    {
        Cache::forget(self::BRANDS_KEY);
    }

    public function clearAll(): void
    {
        $this->clearCategories();
        $this->clearBrands();
    }
}
