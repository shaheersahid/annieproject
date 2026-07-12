<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Image;
use Database\Seeders\OpticalProductSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds optical catalog categories brands products variants and cod settings', function () {
    $this->seed(SettingSeeder::class);
    $this->seed(OpticalProductSeeder::class);

    expect(Category::whereIn('name', ['Frames', 'Lenses', 'Accessories'])->count())->toBe(3)
        ->and(Category::where('name', 'Blue Cut Glasses')->whereHas('parent', fn ($query) => $query->where('name', 'Lenses'))->exists())->toBeTrue()
        ->and(Brand::whereIn('name', ['RayBan', 'Cartier', 'Prada', 'TomFord', 'Qadir Optics'])->count())->toBe(5)
        ->and(Product::whereIn('product_type', ['frame', 'lens', 'accessory', 'service'])->count())->toBeGreaterThanOrEqual(10)
        ->and(ProductVariant::whereNotNull('category_id')->count())->toBeGreaterThan(0)
        ->and(Image::where('path', 'like', '%assets/images/optical/products/%')->count())->toBeGreaterThanOrEqual(10)
        ->and(Image::where('path', 'like', '%demos/demo-7/products/%')->exists())->toBeFalse()
        ->and(Setting::where('key', 'payments.default_method')->value('value'))->toBe('cash_on_delivery')
        ->and(Setting::where('key', 'payments.gateway_enabled')->value('value'))->toBe('0')
        ->and(Transaction::where('payment_method', 'cash_on_delivery')->count())->toBe(12)
        ->and(Transaction::whereNotNull('gateway_reference')->exists())->toBeFalse();
});
