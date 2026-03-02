<?php

declare(strict_types=1);

use Erp\Inventory\Http\Controllers\StockMovementController;
use Erp\Inventory\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('stock', [StockMovementController::class, 'stock'])->name('stock');
Route::apiResource('warehouses', WarehouseController::class);
Route::apiResource('stock-movements', StockMovementController::class)->only(['index', 'show']);