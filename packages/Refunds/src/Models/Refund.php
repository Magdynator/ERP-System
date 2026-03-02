<?php

declare(strict_types=1);

namespace Erp\Refunds\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Refund extends BaseModel
{
    protected $table = 'refunds';

    protected $fillable = [
        'refund_number',
        'sale_id',
        'warehouse_id',
        'branch_id',
        'currency',
        'status',
        'notes',
        'refund_date',
    ];

    protected $casts = [
        'refund_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RefundItem::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items()->sum(\Illuminate\Support\Facades\DB::raw('selling_price * quantity'));
    }
}
