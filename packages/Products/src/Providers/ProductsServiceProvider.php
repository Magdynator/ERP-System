<?php

declare(strict_types=1);

namespace Erp\Products\Providers;

use Erp\Products\Contracts\ProductServiceInterface;
use Erp\Products\Services\ProductService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(\Erp\Products\Contracts\CategoryServiceInterface::class, \Erp\Products\Services\CategoryService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/products.php',
            'products'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/products.php' => config_path('products.php'),
            ], 'products-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/web.php');
        });

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        });
    }
}
