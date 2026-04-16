<?php

declare(strict_types=1);

namespace Erp\Products\Contracts;

use Erp\Products\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductServiceInterface
{
    public function getPaginatedProducts(int $perPage = 15, ?int $categoryId = null, bool $activeOnly = false): LengthAwarePaginator;
    public function createProduct(array $data): Product;
    public function updateProduct(Product $product, array $data): Product;
    public function deleteProduct(Product $product): void;
    /**
     * Get product data for sale snapshot (cost_price, selling_price, tax_percentage).
     * Returns null if product not found or inactive.
     *
     * @return array{id: int, cost_price: float, selling_price: float, tax_percentage: float}|null
     */
    public function getForSale(int $productId): ?array;
}
