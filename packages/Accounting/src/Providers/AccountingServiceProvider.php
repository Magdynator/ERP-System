<?php

declare(strict_types=1);

namespace Erp\Accounting\Providers;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Services\AccountingService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountingServiceInterface::class, AccountingService::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/accounting.php',
            'accounting'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/accounting.php' => config_path('accounting.php'),
            ], 'accounting-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        });
    }
}
