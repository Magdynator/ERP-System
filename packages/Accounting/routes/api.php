<?php

declare(strict_types=1);

use Erp\Accounting\Http\Controllers\AccountController;
use Erp\Accounting\Http\Controllers\JournalEntryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('accounts', AccountController::class);
Route::apiResource('journal-entries', JournalEntryController::class)->only(['index', 'show', 'store']);
