<?php

declare(strict_types=1);

namespace Erp\Products\Contracts;

use Erp\Products\Models\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;

    public function findBySku(string $sku): ?Product;

    public function create(array $data): Product;

    public function update(Product $product, array $data): Product;
}
