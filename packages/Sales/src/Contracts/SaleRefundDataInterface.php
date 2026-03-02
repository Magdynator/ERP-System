<?php

declare(strict_types=1);

namespace Erp\Sales\Contracts;

interface SaleRefundDataInterface
{
    /**
     * Get sale with items for refund processing. Returns null if sale not found.
     *
     * @return array{sale: array{id: int, currency: string, branch_id: ?int}, items: array<int, array{id: int, product_id: int, quantity: float, selling_price: float, cost_price: float}>}|null
     */
    public function getSaleWithItemsForRefund(int $saleId): ?array;
}
