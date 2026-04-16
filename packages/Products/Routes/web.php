<?php

declare(strict_types=1);

use Erp\Products\Http\Controllers\Web\CategoryController;
use Erp\Products\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoryController::class)->names('web.categories');
    Route::resource('products', ProductController::class)->names('web.products');
});
