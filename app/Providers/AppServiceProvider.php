<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        \Illuminate\Support\Facades\Gate::define('manage-users', fn ($user) => false); // only super_admin

        \Illuminate\Support\Facades\Gate::define('view-accounting', fn ($user) => false); // only super_admin

        \Illuminate\Support\Facades\Gate::define('add-warehouse', fn ($user) => false); // only super_admin

        \Illuminate\Support\Facades\Gate::define('view-expenses', fn ($user) => false); // only super_admin

        \Illuminate\Support\Facades\Gate::define('manage-products', function ($user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manage-categories', function ($user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('view-audit-logs', function ($user) {
            return $user->isAdmin();
        });
    }
}
