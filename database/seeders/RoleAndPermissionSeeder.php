<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', [
            'view products',
            'create products',
            'edit products',
            'delete products',
        ])->delete();

        // 2. Define all route-name based permissions and legacy view permissions.
        $routePermissions = [
            // Dashboard
            'admin.dashboard',

            // Categories
            'admin.categories.index',
            'admin.categories.create',
            'admin.categories.store',
            'admin.categories.edit',
            'admin.categories.update',
            'admin.categories.destroy',
            'admin.categories.products',
            'admin.categories.toggle-status',
            'admin.categories.quick-store',

            // Brands
            'admin.brands.index',
            'admin.brands.create',
            'admin.brands.store',
            'admin.brands.edit',
            'admin.brands.update',
            'admin.brands.destroy',
            'admin.brands.show',
            'admin.brands.toggle-active',

            // Tags
            'admin.attributes.index',
            'admin.attributes.create',
            'admin.attributes.store',
            'admin.attributes.edit',
            'admin.attributes.update',
            'admin.attributes.destroy',
            'admin.attributes.toggle-active',

            // Product Attributes
            'admin.product-attributes.index',
            'admin.product-attributes.create',
            'admin.product-attributes.store',
            'admin.product-attributes.edit',
            'admin.product-attributes.update',
            'admin.product-attributes.destroy',
            'admin.product-attributes.toggle-active',

            // Products
            'admin.products.index',
            'admin.products.create',
            'admin.products.store',
            'admin.products.show',
            'admin.products.edit',
            'admin.products.update',
            'admin.products.destroy',
            'admin.products.drafts',
            'admin.products.stock-products',
            'admin.products.stocks.add-stock.create',
            'admin.products.stocks.add-stock.store',
            'admin.products.stocks.order-requests.show',
            'admin.products.stocks.order-requests.add-stock',
            'admin.products.seo.edit',
            'admin.products.seo.update',
            'admin.products.variant-builder-data',
            'admin.products.generate-variants-preview',
            'admin.products.export',
            'admin.products.reorder',
            'admin.products.update-status',

            // Website Orders
            'admin.website-orders',
            'admin.website-orders.show',
            'admin.website-orders.update-status',
            'admin.website-orders.cancel',
            'admin.website-orders.destroy',

            // Finance / Expenses
            'admin.expenses.index',
            'admin.expenses.create',
            'admin.expenses.store',
            'admin.expenses.edit',
            'admin.expenses.update',
            'admin.expenses.destroy',

            // Users
            'admin.users.index',
            'admin.users.create',
            'admin.users.store',
            'admin.users.show',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.destroy',

            // Customers
            'admin.customers.index',

            // Sellers
            'admin.sellers.index',
            'admin.sellers.create',
            'admin.sellers.store',
            'admin.sellers.edit',
            'admin.sellers.update',
            'admin.sellers.destroy',
            'admin.sellers.toggle-active',

            // Roles & Permissions
            'admin.users.roles.index',
            'admin.users.roles.store',
            'admin.users.roles.update',
            'admin.users.roles.destroy',
            'admin.users.roles.permissions.edit',

            // Newsletter
            'admin.newsletter.index',
            'admin.newsletter.destroy',
            'admin.newsletter.send',

            // Contact
            'admin.contact.index',
            'admin.contact.show',
            'admin.contact.updateStatus',
            'admin.contact.destroy',

            // Notifications
            'admin.notifications.index',
            'admin.notifications.markAllRead',
            'admin.notifications.markAsRead',

            // Audit Logs
            'admin.audit-logs.index',

            // Settings
            'admin.settings.index',
            'admin.settings.update',

            // Profile
            'admin.profile.edit',
            'admin.profile.update',
        ];

        $legacyPermissions = [
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            'view orders',
            'edit orders',
            'delete orders',
            'view expenses',
            'create expenses',
            'edit expenses',
            'delete expenses',
            'view newsletter',
            'send newsletter',
            'adjust inventory',
        ];

        $permissions = array_values(array_unique(array_merge($routePermissions, $legacyPermissions)));

        // Create all permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. Create roles and assign permissions

        // Super Admin has unrestricted access via Gate::before.
        $superAdminRole = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web']);

        // Admin — all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Manager — catalog + orders + marketing + notifications + profile
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'admin.dashboard',

            'admin.categories.index',
            'admin.categories.create',
            'admin.categories.store',
            'admin.categories.edit',
            'admin.categories.update',
            'admin.categories.products',
            'admin.categories.toggle-status',
            'admin.categories.quick-store',
            'view categories',
            'create categories',
            'edit categories',

            'admin.brands.index',
            'admin.brands.create',
            'admin.brands.store',
            'admin.brands.edit',
            'admin.brands.update',
            'admin.brands.show',
            'admin.brands.toggle-active',
            'view brands',
            'create brands',
            'edit brands',

            'admin.product-attributes.index',
            'admin.product-attributes.create',
            'admin.product-attributes.store',
            'admin.product-attributes.edit',
            'admin.product-attributes.update',
            'admin.product-attributes.destroy',
            'admin.product-attributes.toggle-active',

            // Tags
            'admin.attributes.index',
            'admin.attributes.create',
            'admin.attributes.store',
            'admin.attributes.edit',
            'admin.attributes.update',
            'admin.attributes.destroy',
            'admin.attributes.toggle-active',

            'admin.products.index',
            'admin.products.create',
            'admin.products.store',
            'admin.products.show',
            'admin.products.edit',
            'admin.products.update',
            'admin.products.drafts',
            'admin.products.stock-products',
            'admin.products.stocks.add-stock.create',
            'admin.products.stocks.add-stock.store',
            'admin.products.stocks.order-requests.show',
            'admin.products.stocks.order-requests.add-stock',
            'admin.products.seo.edit',
            'admin.products.seo.update',
            'admin.products.variant-builder-data',
            'admin.products.generate-variants-preview',
            'admin.products.export',
            'admin.products.reorder',
            'admin.products.update-status',
            'admin.website-orders',
            'admin.website-orders.show',
            'admin.website-orders.update-status',
            'admin.website-orders.cancel',
            'view orders',
            'edit orders',

            'admin.sellers.index',
            'admin.sellers.create',
            'admin.sellers.store',
            'admin.sellers.edit',
            'admin.sellers.update',

            'admin.customers.index',

            'admin.expenses.index',
            'view expenses',

            'admin.newsletter.index',
            'admin.newsletter.destroy',
            'admin.newsletter.send',
            'view newsletter',
            'send newsletter',

            'admin.contact.index',
            'admin.contact.show',
            'admin.contact.updateStatus',
            'admin.contact.destroy',

            'admin.notifications.index',
            'admin.notifications.markAllRead',
            'admin.notifications.markAsRead',

            'admin.profile.edit',
            'admin.profile.update',
        ]);

        // Staff — orders + notifications + profile only
        $staffRole = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'admin.dashboard',
            'admin.website-orders',
            'admin.website-orders.show',
            'view orders',
            'admin.notifications.index',
            'admin.notifications.markAllRead',
            'admin.notifications.markAsRead',
            'admin.profile.edit',
            'admin.profile.update',
        ]);

        // Customer
        Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // 4. Seed test users

        // Clean up old emails if they exist to avoid stale duplicate accounts
        User::whereIn('email', [
            'qadeeroptical@gmail.com',
            'qadeeropticals@gmail.com',
            'admin@qadeeroptics.com',
            'admin@smartcomfortfinds.com',
            'manager@qadeeroptics.com',
            'manager@raimall.com',
            'staff@qadeeroptics.com',
            'customer@qadeeroptics.com'
        ])->delete();

        $superAdminUser = User::updateOrCreate(
            ['email' => 'superadmin@annie.com'],
            ['name' => 'Super Admin', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $superAdminUser->syncRoles([$superAdminRole]);

        $superAdminUser2 = User::updateOrCreate(
            ['email' => 'superadmin2@annie.com'],
            ['name' => 'Super Admin 2', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $superAdminUser2->syncRoles([$superAdminRole]);

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@annie.com'],
            ['name' => 'Annie', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $adminUser->syncRoles(['admin']);

        $annieAdminUser = User::updateOrCreate(
            ['email' => 'annieadmin@annie.com'],
            ['name' => 'Annie Admin', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $annieAdminUser->syncRoles([$superAdminRole]);

        $managerUser = User::updateOrCreate(
            ['email' => 'manager@annie.com'],
            ['name' => 'Manager User', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $managerUser->syncRoles(['manager']);

        $legacyManagerUser = User::updateOrCreate(
            ['email' => 'legacymanager@annie.com'],
            ['name' => 'Legacy Manager User', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $legacyManagerUser->syncRoles(['manager']);

        $staffUser = User::updateOrCreate(
            ['email' => 'staff@annie.com'],
            ['name' => 'Staff User', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $staffUser->syncRoles(['staff']);

        $customerUser = User::updateOrCreate(
            ['email' => 'customer@annie.com'],
            ['name' => 'Customer User', 'password' => bcrypt('annie123'), 'email_verified_at' => now()]
        );
        $customerUser->syncRoles(['customer']);
    }
}
