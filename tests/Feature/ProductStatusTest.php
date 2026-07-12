<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user and authenticate
        $admin = User::factory()->create();
        $this->actingAs($admin);
    }

    public function test_product_draft_scope_only_returns_drafts(): void
    {
        // Create a draft product
        $draft = Product::create([
            'name' => 'Draft Product',
            'product_type' => 'frame',
            'is_draft' => true,
            'is_active' => true,
            'base_price' => 1000,
        ]);

        // Create a published product
        $published = Product::create([
            'name' => 'Published Product',
            'product_type' => 'frame',
            'is_draft' => false,
            'is_active' => true,
            'base_price' => 1200,
        ]);

        $drafts = Product::draft()->get();
        $this->assertCount(1, $drafts);
        $this->assertEquals($draft->id, $drafts->first()->id);

        $publishedProducts = Product::published()->get();
        $this->assertCount(1, $publishedProducts);
        $this->assertEquals($published->id, $publishedProducts->first()->id);
    }

    public function test_product_active_and_out_of_stock_saving(): void
    {
        $product = Product::create([
            'name' => 'Test Product',
            'product_type' => 'frame',
            'is_draft' => false,
            'is_active' => true,
            'out_of_stock' => true,
            'base_price' => 1500,
        ]);

        $this->assertTrue($product->is_active);
        $this->assertTrue($product->out_of_stock);
        $this->assertFalse($product->is_draft);
    }
}
