<?php

declare(strict_types=1);

use Erp\Sales\Http\Controllers\Web\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('web.sales.invoice');
    Route::resource('sales', SaleController::class)->except(['destroy'])->names('web.sales');
});
