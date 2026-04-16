<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Controllers\Web;

use Erp\Inventory\Http\Controllers\Controller;
use Erp\Inventory\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function index(Request $request): View
    {
        $productId   = $request->integer('product_id');
        $warehouseId = $request->integer('warehouse_id');

        $products   = \Erp\Products\Models\Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->orderBy('name')->get();

        $stock = $this->stockMovementService->getStockForView($productId, $warehouseId ?: null, $products, $warehouses);

        return view('stock.index', compact('products', 'warehouses', 'stock'));
    }
}
