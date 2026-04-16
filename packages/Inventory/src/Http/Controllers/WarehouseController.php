<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Controllers;

use Erp\Inventory\Contracts\WarehouseServiceInterface;
use Erp\Inventory\Http\Requests\StoreWarehouseRequest;
use Erp\Inventory\Http\Requests\UpdateWarehouseRequest;
use Erp\Inventory\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(
        protected WarehouseServiceInterface $warehouseService
    ) {}
    /**
     * @OA\Get(
     *     path="/api/v1/warehouses",
     *     summary="List all warehouses",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
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
     *                         {"id": 1, "name": "Main Warehouse", "code": "WH-MAIN", "is_active": true}
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
        $warehouses = $this->warehouseService->getPaginatedWarehouses(
            $request->integer('per_page', 15),
            $request->boolean('active_only')
        );

        return response()->json(['data' => $warehouses]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/warehouses/{warehouse}",
     *     summary="Get warehouse details",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         description="Warehouse ID",
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
     *                     "name": "Main Warehouse",
     *                     "code": "WH-MAIN",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Warehouse not found")
     * )
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        return response()->json(['data' => $warehouse]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/warehouses",
     *     summary="Create new warehouse",
     *     tags={"Inventory"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Warehouse created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Warehouse created.",
     *                 "data": {
     *                     "id": 2,
     *                     "name": "East Coast Hub",
     *                     "code": "WH-EAST",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreWarehouseRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $warehouse = $this->warehouseService->createWarehouse($validated);

        return response()->json(['data' => $warehouse, 'message' => 'Warehouse created.'], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/warehouses/{warehouse}",
     *     summary="Update existing warehouse",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         description="Warehouse ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Warehouse updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "name": "Global HQ Warehouse",
     *                     "code": "WH-MAIN",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Warehouse not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): JsonResponse
    {
        $validated = $request->validated();

        $warehouse = $this->warehouseService->updateWarehouse($warehouse, $validated);

        return response()->json(['data' => $warehouse, 'message' => 'Warehouse updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/warehouses/{warehouse}",
     *     summary="Delete warehouse",
     *     tags={"Inventory"},
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         description="Warehouse ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Warehouse deleted successfully"),
     *     @OA\Response(response=404, description="Warehouse not found")
     * )
     */
    public function destroy(Warehouse $warehouse): JsonResponse
    {
        $this->warehouseService->deleteWarehouse($warehouse);

        return response()->json(['message' => 'Warehouse deleted.'], 204);
    }
}
