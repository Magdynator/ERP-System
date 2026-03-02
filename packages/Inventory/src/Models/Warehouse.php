<?php

declare(strict_types=1);

namespace Erp\Inventory\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends BaseModel
{
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'code',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
