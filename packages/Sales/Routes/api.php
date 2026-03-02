<?php

declare(strict_types=1);

use Erp\Sales\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::apiResource('sales', SaleController::class);
