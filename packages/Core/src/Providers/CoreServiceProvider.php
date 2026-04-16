<?php

declare(strict_types=1);

namespace Erp\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/core.php',
            'core'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/core.php' => config_path('core.php'),
            ], 'core-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Schema::defaultStringLength(191);

        // Load web routes
        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        });

        // Load API routes
        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        });
    }
}

