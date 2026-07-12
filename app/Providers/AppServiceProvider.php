<?php

namespace App\Providers;

use App\Services\FrontendCacheService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\DataTableServiceInterface::class,
            \App\Services\DataTableService::class
        );

        $this->app->bind(
            \App\Contracts\VariationGeneratorServiceInterface::class,
            \App\Services\VariationGeneratorService::class
        );

        $this->app->bind(
            \App\Contracts\DealCalculationServiceInterface::class,
            \App\Services\DealCalculationService::class
        );

        $this->app->bind(
            \App\Contracts\ProductPriceServiceInterface::class,
            \App\Services\ProductPriceService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();

        try {
            $frontendNavCategories = Schema::hasTable('categories')
                ? app(FrontendCacheService::class)->getNavCategories()
                : collect();
        } catch (Throwable) {
            $frontendNavCategories = collect();
        }

        View::share('frontendNavCategories', $frontendNavCategories);

        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super admin')) {
                return true;
            }
        });
    }
}
