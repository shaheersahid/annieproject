<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProductPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_permissions_cover_admin_product_routes(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $productRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => is_string($name) && str_starts_with($name, 'admin.products.'))
            ->values();

        $seededPermissionNames = Permission::query()
            ->whereIn('name', $productRouteNames)
            ->pluck('name');

        $this->assertSame(
            [],
            $productRouteNames->diff($seededPermissionNames)->values()->all()
        );

        $this->assertDatabaseMissing(Permission::class, ['name' => 'create products']);
        $this->assertDatabaseMissing(Permission::class, ['name' => 'edit products']);
        $this->assertDatabaseMissing(Permission::class, ['name' => 'delete products']);
        $this->assertDatabaseMissing(Permission::class, ['name' => 'view products']);
    }

    public function test_manager_can_create_product_with_seeded_product_store_permission(): void
    {
        $this->seed(RoleAndPermissionSeeder::class);

        $manager = User::query()->where('email', 'manager@raimall.com')->firstOrFail();
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $response = $this->actingAs($manager)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'product_type' => 'frame',
            'has_variants' => false,
            'category_ids' => [$category->id],
            'base_price' => 1500,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas(Product::class, [
            'name' => 'Test Product',
            'product_type' => 'frame',
            'base_price' => 1500,
            'stock' => 5,
        ]);
    }

    public function test_legacy_create_product_permission_does_not_allow_product_route_access(): void
    {
        Permission::firstOrCreate(['name' => 'create products', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions(['create products']);

        $manager = User::factory()->create();
        $manager->assignRole($managerRole);

        $category = Category::create([
            'name' => 'Legacy Category',
            'slug' => 'legacy-category',
            'is_active' => true,
        ]);

        $this->actingAs($manager)
            ->get(route('admin.products.create'))
            ->assertForbidden();

        $this->actingAs($manager)->post(route('admin.products.store'), [
            'name' => 'Legacy Permission Product',
            'product_type' => 'frame',
            'has_variants' => false,
            'category_ids' => [$category->id],
            'base_price' => 1800,
            'stock' => 3,
            'is_active' => true,
        ])->assertForbidden();

        $this->assertDatabaseMissing(Product::class, [
            'name' => 'Legacy Permission Product',
        ]);
    }
}
