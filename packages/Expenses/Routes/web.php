<?php

declare(strict_types=1);

use Erp\Expenses\Http\Controllers\Web\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:view-expenses'])->group(function () {
    Route::resource('expenses', ExpenseController::class)->names('web.expenses');
});
