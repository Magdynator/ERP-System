<?php

declare(strict_types=1);

namespace Erp\Products\Services;

use Erp\Products\Contracts\ProductServiceInterface;
use Erp\Products\Models\Product;

class ProductService implements ProductServiceInterface
{
    public function getForSale(int $productId): ?array
    {
        $product = Product::where('id', $productId)->where('is_active', true)->first();

        if (! $product) {
            return null;
        }

        return [
            'id' => $product->id,
            'cost_price' => (float) $product->cost_price,
            'selling_price' => (float) $product->selling_price,
            'tax_percentage' => (float) $product->tax_percentage,
        ];
    }
}
