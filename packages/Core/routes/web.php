<?php

declare(strict_types=1);

use Erp\Core\Http\Controllers\Web\LoginController;
use Erp\Core\Http\Controllers\Web\RegisterController;
use Erp\Core\Http\Controllers\Web\DashboardController;
use Erp\Core\Http\Controllers\Web\AuditLogController;
use Erp\Core\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show'])->names('web.audit-logs')->middleware('can:view-audit-logs');
    Route::resource('users', UserController::class)->except(['show'])->names('web.users')->middleware('can:manage-users');
});
