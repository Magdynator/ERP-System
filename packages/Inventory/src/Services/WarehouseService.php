<?php

declare(strict_types=1);

namespace Erp\Inventory\Services;

use Erp\Inventory\Contracts\WarehouseServiceInterface;
use Erp\Inventory\Models\Warehouse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class WarehouseService implements WarehouseServiceInterface
{
    public function getPaginatedWarehouses(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator
    {
        $cacheKey = "warehouses_page_{$perPage}_active_{$activeOnly}_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 3600, function () use ($perPage, $activeOnly) {
            $query = Warehouse::query();
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }

            return $query->orderBy('name')->paginate($perPage);
        });
    }

    public function createWarehouse(array $data): Warehouse
    {
        $data['is_active'] = $data['is_active'] ?? true;
        
        $warehouse = Warehouse::create($data);
        $this->clearCache();

        return $warehouse;
    }

    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse->update($data);
        $this->clearCache();

        return $warehouse->fresh();
    }

    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::flush();
    }
}
