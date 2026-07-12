<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductBrowseController;
use App\Http\Controllers\Admin;

// Socialite Routes
Route::controller(SocialiteController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle')->name('google.login');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
});


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/go/{product}/{platform}', [AffiliateController::class, 'redirect'])->name('affiliate.redirect');
Route::get('/products', [ProductBrowseController::class, 'index'])->name('product-list');
Route::get('/products/{product:slug}', [ProductBrowseController::class, 'show'])->name('product-detail');

Route::get('/wishlist', function () {
    return view('content.wishlist');
})->name('wishlist');

Route::get('/cart', function () {
    return view('content.cart');
})->name('cart');

Route::get('/about', function () {
    return view('content.about');
})->name('about');

Route::get('/contact', function () {
    return view('content.contact');
})->name('contact');

Route::get('/checkout', function () {
    return view('content.checkout');
})->name('checkout');

// Customer Account Routes
Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->controller(AccountController::class)->group(function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/orders', 'orders')->name('orders');
    Route::get('/notifications', 'notifications')->name('notifications');
    Route::get('/profile', 'profile')->name('profile');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/mark-all-read', [Admin\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{id}/read', [Admin\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Audit Logs
    Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Product Review
    Route::prefix('product-review')->name('product-review.')->group(function () {
        Route::get('/', [Admin\ProductReviewController::class, 'index'])->name('index');
        Route::patch('/{review}', [Admin\ProductReviewController::class, 'update'])->name('update');
    });

    // Categories & Attributes
    Route::resource('attributes', Admin\ProductTagController::class)->except(['show']);
    Route::patch('attributes/{attribute}/toggle-active', [Admin\ProductTagController::class, 'toggleActive'])
        ->name('attributes.toggle-active');
    Route::resource('product-attributes', Admin\ProductAttributeController::class)->except(['show']);
    Route::patch('product-attributes/{product_attribute}/toggle-active', [Admin\ProductAttributeController::class, 'toggleActive'])
        ->name('product-attributes.toggle-active');

    // Manage Inventory
    Route::prefix('manage-inventory')->name('manage-inventory.')->group(function () {
        Route::get('/', [Admin\InventoryController::class, 'index'])->name('index');
        Route::get('/add-stock', [Admin\InventoryAdjustmentController::class, 'create'])->name('add-stock.create');
        Route::post('/add-stock', [Admin\InventoryAdjustmentController::class, 'storeSelected'])->name('add-stock.store');
        Route::get('/{product}', [Admin\InventoryController::class, 'show'])->name('show');
        Route::post('/{product}/add-stock', [Admin\InventoryAdjustmentController::class, 'store'])->name('add-stock');
    });

    // Order Management
    Route::prefix('order-management')->name('order-management.')->group(function () {
        Route::get('/orders', [Admin\OrderManagementController::class, 'index'])->name('orders');
        Route::get('/returns-refunds', [Admin\ReturnRefundController::class, 'index'])->name('returns-refunds');
        Route::get('/abandoned-carts', [Admin\AbandonedCartController::class, 'index'])->name('abandoned-carts');
        Route::get('/transactions', [Admin\TransactionController::class, 'index'])->name('transactions');
    });

    // Reports & Analytics
    Route::prefix('reports-analytics')->name('reports-analytics.')->group(function () {
        Route::get('/sales-reports', [Admin\ReportsAnalyticsController::class, 'salesReports'])->name('sales-reports');
        Route::get('/seller-performance', [Admin\ReportsAnalyticsController::class, 'sellerPerformance'])->name('seller-performance');
        Route::get('/top-products', [Admin\ReportsAnalyticsController::class, 'topProducts'])->name('top-products');
        Route::get('/categories', [Admin\ReportsAnalyticsController::class, 'categories'])->name('categories');
    });

    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [Admin\UserController::class, 'index'])
            ->name('index');
        Route::get('/create', [Admin\UserController::class, 'create'])
            ->name('create');
        Route::post('/', [Admin\UserController::class, 'store'])
            ->name('store');
        Route::get('/roles', [Admin\RoleController::class, 'index'])
            ->name('roles.index');
        Route::post('/roles', [Admin\RoleController::class, 'store'])
            ->name('roles.store');
        Route::get('/roles/{role}/permissions', [Admin\RoleController::class, 'editPermissions'])
            ->name('roles.permissions.edit');
        Route::put('/roles/{role}', [Admin\RoleController::class, 'update'])
            ->name('roles.update');
        Route::delete('/roles/{role}', [Admin\RoleController::class, 'destroy'])
            ->name('roles.destroy');
        Route::get('/{user}', [Admin\UserController::class, 'show'])
            ->name('show');
        Route::get('/{user}/edit', [Admin\UserController::class, 'edit'])
            ->name('edit');
        Route::put('/{user}', [Admin\UserController::class, 'update'])
            ->name('update');
        Route::delete('/{user}', [Admin\UserController::class, 'destroy'])
            ->name('destroy');
        Route::get('/customers/list', [Admin\UserController::class, 'index'])->name('customers');
    });

    Route::resource('sellers', Admin\SellerController::class)->except(['show']);
    Route::patch('sellers/{seller}/toggle-active', [Admin\SellerController::class, 'toggleActive'])
        ->name('sellers.toggle-active');
    Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');

    // Categories
    Route::get('categories/{category}/products', [Admin\CategoryController::class, 'products'])->name('categories.products');
    Route::post('categories/toggle-status', [Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/quick-store', [Admin\CategoryController::class, 'quickStore'])->name('categories.quick-store');
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/drafts', [Admin\ProductController::class, 'drafts'])->name('drafts');
        Route::get('/stocks', [Admin\ProductController::class, 'stockProducts'])->name('stock-products');
        Route::get('/stocks/add-stock', [Admin\InventoryAdjustmentController::class, 'create'])->name('stocks.add-stock.create');
        Route::post('/stocks/add-stock', [Admin\InventoryAdjustmentController::class, 'storeSelected'])->name('stocks.add-stock.store');
        Route::get('/stocks/order-requests/{product}', [Admin\InventoryController::class, 'show'])->name('stocks.order-requests.show');
        Route::post('/stocks/order-requests/{product}/add-stock', [Admin\InventoryAdjustmentController::class, 'store'])->name('stocks.order-requests.add-stock');
        Route::get('/export', [Admin\ProductController::class, 'export'])->name('export');
        Route::post('/reorder', [Admin\ProductController::class, 'reorder'])->name('reorder');
        Route::post('/update-status', [Admin\ProductController::class, 'updateStatus'])->name('update-status');
        
        Route::get('/{product}/seo', [Admin\ProductController::class, 'seoEdit'])->name('seo.edit');
        Route::put('/{product}/seo', [Admin\ProductController::class, 'seoUpdate'])->name('seo.update');
        Route::get('/{product}/variant-builder-data', [Admin\ProductController::class, 'variantBuilderData'])->name('variant-builder-data');
        Route::post('/{product}/generate-variants-preview', [Admin\ProductController::class, 'generateVariantsPreview'])->name('generate-variants-preview');
    });
    Route::resource('products', Admin\ProductController::class);

    Route::resource('brands', Admin\BrandController::class);
    Route::patch('brands/{brand}/toggle-active', [Admin\BrandController::class, 'toggleActive'])
        ->name('brands.toggle-active');

    // Orders
    Route::get('/website-orders', [Admin\OrderController::class, 'websiteOrders'])->name('website-orders');
    Route::prefix('website-orders')->name('website-orders.')->group(function () {
        Route::get('/{order}', [Admin\OrderController::class, 'showWebsiteOrder'])->name('show');
        Route::patch('/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('update-status');
        Route::post('/{order}/cancel', [Admin\OrderController::class, 'cancel'])->name('cancel');
        Route::delete('/{order}', [Admin\OrderController::class, 'destroy'])->name('destroy');
    });

    // Newsletter Subscribers
    Route::resource('newsletter', Admin\NewsletterController::class)->only(['index', 'destroy']);
    Route::post('newsletter/send', [Admin\NewsletterController::class, 'send'])->name('newsletter.send');

    // Contact Submissions
    Route::get('/contact', [Admin\ContactController::class, 'index'])->name('contact.index');
    Route::get('/contact/{id}', [Admin\ContactController::class, 'show'])->name('contact.show');
    Route::patch('/contact/{id}/status', [Admin\ContactController::class, 'updateStatus'])->name('contact.updateStatus');
    Route::delete('/contact/{id}', [Admin\ContactController::class, 'destroy'])->name('contact.destroy');

    // Settings
    Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.index');
    Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    // Profile Routes
    Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
});
