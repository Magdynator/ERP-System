<?php

declare(strict_types=1);

use Erp\Refunds\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;

Route::apiResource('refunds', RefundController::class);
