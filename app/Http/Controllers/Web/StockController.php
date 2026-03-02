<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Inventory\Contracts\InventoryServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockController extends Controller
{
    public function __construct(
        protected InventoryServiceInterface $inventory
    ) {}

    public function index(Request $request): View
    {
        $productId = $request->integer('product_id');
        $warehouseId = $request->integer('warehouse_id');

        $products = \Erp\Products\Models\Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->orderBy('name')->get();

        $stock = [];
        if ($productId && $warehouseId) {
            $stock[] = [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $this->inventory->getStock($productId, $warehouseId),
                'product' => $products->firstWhere('id', $productId),
                'warehouse' => $warehouses->firstWhere('id', $warehouseId),
            ];
        } elseif ($productId) {
            foreach ($warehouses as $w) {
                $stock[] = [
                    'product_id' => $productId,
                    'warehouse_id' => $w->id,
                    'quantity' => $this->inventory->getStock($productId, $w->id),
                    'product' => $products->firstWhere('id', $productId),
                    'warehouse' => $w,
                ];
            }
        }

        return view('stock.index', compact('products', 'warehouses', 'stock'));
    }
}
