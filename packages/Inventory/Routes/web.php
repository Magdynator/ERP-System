<?php

declare(strict_types=1);

use Erp\Inventory\Http\Controllers\Web\WarehouseController;
use Erp\Inventory\Http\Controllers\Web\StockController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('warehouses', WarehouseController::class)->names('web.warehouses');
    Route::get('stock', [StockController::class, 'index'])->name('web.stock.index');
});
