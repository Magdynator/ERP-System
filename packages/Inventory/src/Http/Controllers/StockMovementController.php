<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Controllers;

use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Inventory\Services\StockMovementService;
use Erp\Inventory\Http\Requests\GetStockRequest;
use Erp\Inventory\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function __construct(
        protected InventoryServiceInterface $inventoryService,
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/stock-movements",
     *     summary="List all stock movements",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         description="Filter by warehouse ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Filter by product ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "current_page": 1,
     *                     "data": {
     *                         {
     *                             "id": 1, "product_id": 1, "warehouse_id": 1, 
     *                             "quantity": 50, "type": "in", "reference_type": "purchase",
     *                             "product": {"id": 1, "name": "MacBook Pro", "sku": "LAP-MBP-14"},
     *                             "warehouse": {"id": 1, "name": "Main Warehouse"}
     *                         }
     *                     },
     *                     "total": 1
     *                 }
     *             }
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $movements = $this->stockMovementService->getPaginatedMovements(
            $request->integer('per_page', 15),
            $request->filled('warehouse_id') ? $request->integer('warehouse_id') : null,
            $request->filled('product_id') ? $request->integer('product_id') : null
        );

        return response()->json(['data' => $movements]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stock-movements/{stockMovement}",
     *     summary="Get stock movement details",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="stockMovement",
     *         in="path",
     *         description="Stock Movement ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1, 
     *                     "product_id": 1, 
     *                     "warehouse_id": 1, 
     *                     "quantity": 50, 
     *                     "type": "in", 
     *                     "reference_type": "purchase",
     *                     "reference_id": 101,
     *                     "product": {"id": 1, "name": "MacBook Pro", "sku": "LAP-MBP-14"},
     *                     "warehouse": {"id": 1, "name": "Main Warehouse"}
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Stock Movement not found")
     * )
     */
    public function show(StockMovement $stockMovement): JsonResponse
    {
        $stockMovement->load(['warehouse', 'product']);

        return response()->json(['data' => $stockMovement]);
    }

    /** Current stock for a product in a warehouse (or all warehouses). */
    /**
     * @OA\Get(
     *     path="/api/v1/stock",
     *     summary="Get current stock levels",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="warehouse_id",
     *         in="query",
     *         description="Warehouse ID (optional, returns all if omitted)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "product_id": 1,
     *                     "stock_by_warehouse": {
     *                         {"warehouse_id": 1, "warehouse_name": "Main Warehouse", "quantity": 150},
     *                         {"warehouse_id": 2, "warehouse_name": "East Coast Hub", "quantity": 50}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function stock(GetStockRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $productId = $request->integer('product_id');
        $warehouseId = $request->integer('warehouse_id');

        if ($warehouseId) {
            $qty = $this->inventoryService->getStock($productId, $warehouseId);

            return response()->json(['data' => ['product_id' => $productId, 'warehouse_id' => $warehouseId, 'quantity' => $qty]]);
        }

        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->get();
        $stock = [];
        foreach ($warehouses as $w) {
            $stock[] = [
                'warehouse_id' => $w->id,
                'warehouse_name' => $w->name,
                'quantity' => $this->inventoryService->getStock($productId, $w->id),
            ];
        }

        return response()->json(['data' => ['product_id' => $productId, 'stock_by_warehouse' => $stock]]);
    }
}
