<?php

declare(strict_types=1);

namespace Erp\Inventory\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends BaseModel
{
    public const TYPE_IN = 'IN';
    public const TYPE_OUT = 'OUT';

    protected $table = 'stock_movements';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'type',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Erp\Products\Models\Product::class);
    }

    public function isIn(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    public function isOut(): bool
    {
        return $this->type === self::TYPE_OUT;
    }
}
