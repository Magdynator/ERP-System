<?php

declare(strict_types=1);

use Erp\Expenses\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::apiResource('expenses', ExpenseController::class);
