<?php

declare(strict_types=1);

namespace Erp\Expenses\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends BaseModel
{
    protected $table = 'expenses';

    protected $fillable = [
        'expense_category_id',
        'amount',
        'currency',
        'expense_date',
        'vendor_name',
        'vendor_reference',
        'description',
        'branch_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }
}
