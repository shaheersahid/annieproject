<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an optical product with a categorized variant', function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $manager = User::query()->where('email', 'manager@raimall.com')->firstOrFail();
    $frames = Category::create(['name' => 'Frames', 'slug' => 'frames', 'is_active' => true]);
    $luxuryFrames = Category::create(['name' => 'Luxury Frames', 'slug' => 'luxury-frames', 'parent_id' => $frames->id, 'is_active' => true]);
    $attribute = ProductAttribute::create([
        'name' => 'Frame Size',
        'slug' => 'frame-size',
        'value' => "Medium\nWide",
        'input_type' => 'dropdown',
        'is_active' => true,
    ]);

    $response = $this->actingAs($manager)->post(route('admin.products.store'), [
        'name' => 'RayBan Premium Eyewear Frame',
        'product_type' => 'frame',
        'has_variants' => true,
        'category_ids' => [$frames->id],
        'base_price' => 6500,
        'stock' => 0,
        'is_active' => true,
        'variants' => [
            [
                'category_id' => $luxuryFrames->id,
                'attribute_id' => $attribute->id,
                'value' => 'Medium',
                'sku' => 'QO-RB-MED',
                'price' => 6500,
                'stock' => 8,
                'low_stock_threshold' => 3,
                'is_active' => true,
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.products.index'));

    $product = Product::where('name', 'RayBan Premium Eyewear Frame')->firstOrFail();

    expect($product->product_type)->toBe('frame')
        ->and($product->categories()->whereKey($frames->id)->exists())->toBeTrue()
        ->and($product->stock)->toBe(8);

    $variant = ProductVariant::where('sku', 'QO-RB-MED')->firstOrFail();

    expect($variant->category_id)->toBe($luxuryFrames->id)
        ->and($variant->attributes)->toBe(['Frame Size' => 'Medium'])
        ->and($variant->low_stock_threshold)->toBe(3);
});
