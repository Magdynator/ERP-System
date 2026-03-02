<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\JournalEntryController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RefundController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\StockController;
use App\Http\Controllers\Web\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('categories', CategoryController::class)->names('web.categories');
    Route::resource('products', ProductController::class)->names('web.products');
    Route::resource('warehouses', WarehouseController::class)->names('web.warehouses');
    Route::get('stock', [StockController::class, 'index'])->name('web.stock.index');
    Route::resource('accounts', AccountController::class)->names('web.accounts')->middleware('can:view-accounting');
    Route::resource('journal-entries', JournalEntryController::class)->only(['index', 'create', 'store', 'show'])->names('web.journal-entries')->middleware('can:view-accounting');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('web.sales.invoice');
    Route::resource('sales', SaleController::class)->except(['destroy'])->names('web.sales');
    Route::resource('expenses', ExpenseController::class)->names('web.expenses')->middleware('can:view-expenses');
    Route::resource('refunds', RefundController::class)->only(['index', 'create', 'store', 'show'])->names('web.refunds');
    Route::resource('audit-logs', \App\Http\Controllers\Web\AuditLogController::class)->only(['index', 'show'])->names('web.audit-logs')->middleware('can:view-audit-logs');
    Route::resource('users', \App\Http\Controllers\Web\UserController::class)->except(['show'])->names('web.users')->middleware('can:manage-users');
});
