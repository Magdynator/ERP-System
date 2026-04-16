<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Controllers\Web;

use Erp\Inventory\Http\Controllers\Controller;
use Erp\Inventory\Contracts\WarehouseServiceInterface;
use Erp\Inventory\Http\Requests\StoreWarehouseRequest;
use Erp\Inventory\Http\Requests\UpdateWarehouseRequest;
use Erp\Inventory\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WarehouseController extends Controller implements HasMiddleware
{
    public function __construct(
        protected WarehouseServiceInterface $warehouseService
    ) {}
    public static function middleware(): array
    {
        return [
            new Middleware('can:add-warehouse', except: ['index', 'show']),
        ];
    }
    public function index(): View
    {
        $warehouses = $this->warehouseService->getPaginatedWarehouses(15, false)->withQueryString();

        return view('warehouses.index', compact('warehouses'));
    }

    public function create(): View
    {
        return view('warehouses.create');
    }

    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->warehouseService->createWarehouse($validated);

        return redirect()->route('web.warehouses.index')->with('success', 'Warehouse created.');
    }

    public function edit(Warehouse $warehouse): View
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $request->validated();

        $this->warehouseService->updateWarehouse($warehouse, $validated);

        return redirect()->route('web.warehouses.index')->with('success', 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $this->warehouseService->deleteWarehouse($warehouse);

        return redirect()->route('web.warehouses.index')->with('success', 'Warehouse deleted.');
    }
}
