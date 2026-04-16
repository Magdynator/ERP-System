<?php

declare(strict_types=1);

use Erp\Accounting\Http\Controllers\Web\AccountController;
use Erp\Accounting\Http\Controllers\Web\JournalEntryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:view-accounting'])->group(function () {
    Route::resource('accounts', AccountController::class)->names('web.accounts');
    Route::resource('journal-entries', JournalEntryController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->names('web.journal-entries');
});
