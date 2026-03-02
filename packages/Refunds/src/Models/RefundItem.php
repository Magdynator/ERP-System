<?php

declare(strict_types=1);

namespace Erp\Refunds\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefundItem extends BaseModel
{
    protected $table = 'refund_items';

    protected $fillable = [
        'refund_id',
        'sale_item_id',
        'product_id',
        'quantity',
        'cost_price',
        'selling_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function refund(): BelongsTo
    {
        return $this->belongsTo(Refund::class);
    }
}
