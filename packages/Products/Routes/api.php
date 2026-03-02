<?php

declare(strict_types=1);

use Erp\Products\Http\Controllers\CategoryController;
use Erp\Products\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
