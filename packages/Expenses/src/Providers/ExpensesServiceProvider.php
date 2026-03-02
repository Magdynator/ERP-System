<?php

declare(strict_types=1);

namespace Erp\Expenses\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ExpensesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/expenses.php',
            'expenses'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Config/expenses.php' => config_path('expenses.php'),
            ], 'expenses-config');

            $this->loadMigrationsFrom(__DIR__ . '/../../Database/Migrations');
        }

        Route::prefix('api/v1')->middleware('auth:sanctum')->group(function () {
            $this->loadRoutesFrom(__DIR__ . '/../../Routes/api.php');
        });
    }
}
