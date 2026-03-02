<?php

declare(strict_types=1);

namespace Erp\Accounting\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends BaseModel
{
    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'code',
        'type',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expense',
            default => $this->type,
        };
    }
}
