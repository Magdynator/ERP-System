<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1 loaded by each package under api/v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('api.v1.login');
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('api.v1.logout');
        Route::get('health', fn () => response()->json(['status' => 'ok']))->name('api.v1.health');
    });
});
