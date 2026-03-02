<?php

declare(strict_types=1);

namespace Erp\Sales\Providers;

use Erp\Sales\Contracts\SaleRefundDataInterface;
use Erp\Sales\Services\SaleRefundDataService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SalesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SaleRefundDataInterface::class, SaleRefundDataService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/sales.php',
            'sales'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/sales.php' => config_path('sales.php'),
            ], 'sales-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        });
    }
}
