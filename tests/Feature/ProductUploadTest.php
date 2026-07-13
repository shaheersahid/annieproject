<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product_with_thumbnail_and_gallery_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $category = Category::create([
            'name' => 'Upload Test',
            'slug' => 'upload-test',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Uploaded Affiliate Product',
            'product_type' => 'frame',
            'affiliate_platform' => 'amazon',
            'amazon_url' => 'https://www.amazon.com/dp/example',
            'category_ids' => [$category->id],
            'thumbnail' => UploadedFile::fake()->image('main.webp', 1200, 900),
            'images' => [
                UploadedFile::fake()->image('gallery-one.jpg', 1200, 900),
                UploadedFile::fake()->image('gallery-two.png', 1200, 900),
            ],
            'video' => UploadedFile::fake()->create('demo.mp4', 1024, 'video/mp4'),
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHasNoErrors();

        $product = Product::query()->where('name', 'Uploaded Affiliate Product')->firstOrFail();
        $this->assertCount(3, $product->images);
        $this->assertNotNull($product->video_path);
        Storage::disk('public')->assertExists($product->video_path);

        foreach ($product->images as $image) {
            Storage::disk('public')->assertExists($image->path);
        }
    }
}
