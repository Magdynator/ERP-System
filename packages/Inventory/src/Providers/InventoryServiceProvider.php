<?php

declare(strict_types=1);

namespace Erp\Inventory\Providers;

use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Inventory\Services\InventoryService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/inventory.php',
            'inventory'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/inventory.php' => config_path('inventory.php'),
            ], 'inventory-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        });
    }
}
