<?php

declare(strict_types=1);

namespace Erp\Inventory\Services;

use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Inventory\Models\StockMovement;
use Erp\Inventory\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;

class StockMovementService
{
    public function __construct(
        protected InventoryServiceInterface $inventoryService
    ) {}

    public function getPaginatedMovements(int $perPage = 15, ?int $warehouseId = null, ?int $productId = null): LengthAwarePaginator
    {
        $query = StockMovement::query()->with(['warehouse', 'product' => fn ($q) => $q->select('id', 'name', 'sku')]);

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        if ($productId) {
            $query->where('product_id', $productId);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getStockForProduct(int $productId, ?int $warehouseId = null): array
    {
        if ($warehouseId) {
            $qty = $this->inventoryService->getStock($productId, $warehouseId);

            return ['product_id' => $productId, 'warehouse_id' => $warehouseId, 'quantity' => $qty];
        }

        $warehouses = Warehouse::where('is_active', true)->get();
        $stock = [];
        foreach ($warehouses as $w) {
            $stock[] = [
                'warehouse_id'   => $w->id,
                'warehouse_name' => $w->name,
                'quantity'       => $this->inventoryService->getStock($productId, $w->id),
            ];
        }

        return ['product_id' => $productId, 'stock_by_warehouse' => $stock];
    }

    public function getStockForView(int $productId, ?int $warehouseId, $products, $warehouses): array
    {
        $stock = [];
        if ($productId && $warehouseId) {
            $stock[] = [
                'product_id'   => $productId,
                'warehouse_id' => $warehouseId,
                'quantity'     => $this->inventoryService->getStock($productId, $warehouseId),
                'product'      => $products->firstWhere('id', $productId),
                'warehouse'    => $warehouses->firstWhere('id', $warehouseId),
            ];
        } elseif ($productId) {
            foreach ($warehouses as $w) {
                $stock[] = [
                    'product_id'   => $productId,
                    'warehouse_id' => $w->id,
                    'quantity'     => $this->inventoryService->getStock($productId, $w->id),
                    'product'      => $products->firstWhere('id', $productId),
                    'warehouse'    => $w,
                ];
            }
        }

        return $stock;
    }
}
