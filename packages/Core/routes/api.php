<?php

declare(strict_types=1);

use Erp\Core\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.v1.login')->withoutMiddleware('auth:sanctum');
Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.logout');
Route::get('health', fn () => response()->json(['status' => 'ok']))->name('api.v1.health');
