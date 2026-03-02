<?php

declare(strict_types=1);

namespace Erp\Accounting\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends BaseModel
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'description',
        'reference_type',
        'reference_id',
        'currency',
        'branch_id',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class, 'journal_entry_id');
    }

    public function getTotalDebitsAttribute(): float
    {
        return (float) $this->lines()->sum('debit');
    }

    public function getTotalCreditsAttribute(): float
    {
        return (float) $this->lines()->sum('credit');
    }
}
