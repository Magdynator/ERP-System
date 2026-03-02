<?php

declare(strict_types=1);

namespace Erp\Products\Models;

use Erp\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends BaseModel
{
    protected $table = 'products';
    
    protected $appends = ['image_url'];

    protected $fillable = [
        'name',
        'sku',
        'cost_price',
        'selling_price',
        'tax_percentage',
        'category_id',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->image_path);
        }
        
        // Return a modern placeholder if no image exists
        $initial = substr($this->name, 0, 1);
        return "https://ui-avatars.com/api/?name={$initial}&color=6366f1&background=e0e7ff&size=400";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
