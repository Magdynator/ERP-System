<?php

declare(strict_types=1);

namespace Erp\Inventory\Contracts;

use Erp\Inventory\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;

interface WarehouseServiceInterface
{
    public function getPaginatedWarehouses(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator;
    public function createWarehouse(array $data): Warehouse;
    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse;
    public function deleteWarehouse(Warehouse $warehouse): void;
}
