<?php

declare(strict_types=1);

namespace Erp\Sales\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends BaseModel
{
    protected $table = 'sale_items';

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'cost_price',
        'selling_price',
        'tax_percentage',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Erp\Products\Models\Product::class);
    }

    public function getLineTotalAttribute(): float
    {
        return (float) ($this->selling_price * $this->quantity);
    }

    public function getLineCostAttribute(): float
    {
        return (float) ($this->cost_price * $this->quantity);
    }
}
