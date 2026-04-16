<?php

declare(strict_types=1);

use Erp\Refunds\Http\Controllers\Web\RefundController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('refunds', RefundController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->names('web.refunds');
});
