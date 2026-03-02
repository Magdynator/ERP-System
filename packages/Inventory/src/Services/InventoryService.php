<?php

declare(strict_types=1);

namespace Erp\Inventory\Services;

use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Inventory\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService implements InventoryServiceInterface
{
    public function deduct(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId
    ): void {
        $this->ensureNonNegativeStock($productId, $warehouseId, $quantity, 'deduct');

        DB::transaction(function () use ($productId, $warehouseId, $quantity, $referenceType, $referenceId) {
            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'type' => StockMovement::TYPE_OUT,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    public function add(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $referenceType,
        int $referenceId
    ): void {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive for add.');
        }

        DB::transaction(function () use ($productId, $warehouseId, $quantity, $referenceType, $referenceId) {
            StockMovement::create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'type' => StockMovement::TYPE_IN,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    public function getStock(int $productId, int $warehouseId): float
    {
        $in = (float) StockMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', StockMovement::TYPE_IN)
            ->sum('quantity');

        $out = (float) StockMovement::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', StockMovement::TYPE_OUT)
            ->sum('quantity');

        return $in - $out;
    }

    private function ensureNonNegativeStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $action
    ): void {
        $current = $this->getStock($productId, $warehouseId);
        if ($current < $quantity) {
            throw new \InvalidArgumentException(
                "Insufficient stock. Available: {$current}, requested: {$quantity} for {$action}."
            );
        }
    }
}
