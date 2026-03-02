<?php

declare(strict_types=1);

namespace Erp\Products\Contracts;

interface ProductServiceInterface
{
    /**
     * Get product data for sale snapshot (cost_price, selling_price, tax_percentage).
     * Returns null if product not found or inactive.
     *
     * @return array{id: int, cost_price: float, selling_price: float, tax_percentage: float}|null
     */
    public function getForSale(int $productId): ?array;
}
