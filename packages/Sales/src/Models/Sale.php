<?php

declare(strict_types=1);

namespace Erp\Sales\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends BaseModel
{
    protected $table = 'sales';

    protected $fillable = [
        'sale_number',
        'warehouse_id',
        'branch_id',
        'currency',
        'customer_name',
        'customer_email',
        'status',
        'notes',
        'sale_date',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items()->sum(\Illuminate\Support\Facades\DB::raw('selling_price * quantity'));
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->items()->sum(\Illuminate\Support\Facades\DB::raw('cost_price * quantity'));
    }
}
