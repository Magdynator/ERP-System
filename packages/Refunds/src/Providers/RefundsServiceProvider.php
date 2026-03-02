<?php

declare(strict_types=1);

namespace Erp\Refunds\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RefundsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/refunds.php',
            'refunds'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/refunds.php' => config_path('refunds.php'),
            ], 'refunds-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        });
    }
}
