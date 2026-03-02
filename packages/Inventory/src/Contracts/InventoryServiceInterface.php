<?php

declare(strict_types=1);

namespace Erp\Inventory\Contracts;

interface InventoryServiceInterface
{
    /**
     * Deduct stock (e.g. on sale). Creates OUT movement.
     */
    public function deduct(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId
    ): void;

    /**
     * Add stock (e.g. on refund). Creates IN movement.
     */
    public function add(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId
    ): void;

    /**
     * Get current stock for product in warehouse (calculated from movements).
     */
    public function getStock(int $productId, int $warehouseId): float;
}
