<?php

declare(strict_types=1);

namespace Erp\Sales\Http\Controllers;

use Erp\Sales\Http\Requests\StoreSaleRequest;
use Erp\Sales\Models\Sale;
use Erp\Sales\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/sales",
     *     summary="List all sales",
     *     tags={"Sales"},
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
     *                         {"id": 1, "sale_number": "SALE-0001", "total": 1999.00, "customer_name": "John Doe", "status": "completed"}
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
        $sales = Sale::with(['items', 'payments'])
            ->orderByDesc('sale_date')
            ->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $sales]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sales/{sale}",
     *     summary="Get sale details",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="sale",
     *         in="path",
     *         description="Sale ID",
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
     *                     "sale_number": "SALE-0001",
     *                     "total": 1999.00,
     *                     "customer_name": "John Doe",
     *                     "status": "completed",
     *                     "items": {
     *                         {"id": 1, "product_id": 1, "quantity": 1, "selling_price": 1999.00}
     *                     },
     *                     "payments": {
     *                         {"id": 1, "account_id": 1, "amount": 1999.00, "method": "cash"}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['items', 'payments']);

        return response()->json(['data' => $sale]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sales",
     *     summary="Create new sale",
     *     tags={"Sales"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"warehouse_id", "items"},
     *             @OA\Property(property="warehouse_id", type="integer"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="number"),
     *                     @OA\Property(property="unit_price", type="number")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="payments",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="account_id", type="integer"),
     *                     @OA\Property(property="amount", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="customer_name", type="string"),
     *             @OA\Property(property="customer_email", type="string"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="branch_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sale created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Sale created successfully.",
     *                 "data": {
     *                     "id": 2,
     *                     "sale_number": "SALE-0002",
     *                     "total": 150.00,
     *                     "customer_name": "Jane Smith"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreSaleRequest $request): JsonResponse
    {
        $sale = $this->saleService->createSale(
            warehouseId: $request->validated('warehouse_id'),
            items: $request->validated('items'),
            payments: $request->validated('payments', []),
            customerName: $request->validated('customer_name'),
            customerEmail: $request->validated('customer_email'),
            currency: $request->validated('currency'),
            branchId: $request->validated('branch_id')
        );

        return response()->json([
            'data' => $sale->load(['items', 'payments']),
            'message' => 'Sale created successfully.',
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/sales/{sale}",
     *     summary="Update sale status/notes",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="sale",
     *         in="path",
     *         description="Sale ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="notes", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sale updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Sale updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "status": "shipped",
     *                     "notes": "Package sent via FedEx"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function update(Request $request, Sale $sale): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $sale->update($validated);

        return response()->json(['data' => $sale->fresh(['items', 'payments']), 'message' => 'Sale updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/sales/{sale}",
     *     summary="Delete sale record",
     *     tags={"Sales"},
     *     @OA\Parameter(
     *         name="sale",
     *         in="path",
     *         description="Sale ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Sale deleted successfully"),
     *     @OA\Response(response=404, description="Sale not found")
     * )
     */
    public function destroy(Sale $sale): JsonResponse
    {
        $sale->delete();

        return response()->json(['message' => 'Sale deleted.'], 204);
    }
}
