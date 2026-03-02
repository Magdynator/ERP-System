<?php

declare(strict_types=1);

namespace Erp\Sales\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    protected $table = 'payments';

    protected $fillable = [
        'sale_id',
        'amount',
        'method',
        'reference',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
