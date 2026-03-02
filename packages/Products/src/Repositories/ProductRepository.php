<?php

declare(strict_types=1);

namespace Erp\Products\Repositories;

use Erp\Products\Contracts\ProductRepositoryInterface;
use Erp\Products\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findBySku(string $sku): ?Product
    {
        return Product::where('sku', $sku)->first();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh();
    }
}
