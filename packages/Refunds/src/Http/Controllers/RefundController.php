<?php

declare(strict_types=1);

namespace Erp\Refunds\Http\Controllers;

use Erp\Refunds\Http\Requests\StoreRefundRequest;
use Erp\Refunds\Http\Requests\UpdateRefundRequest;
use Erp\Refunds\Models\Refund;
use Erp\Refunds\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    public function __construct(
        protected RefundService $refundService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/refunds",
     *     summary="List all refunds",
     *     tags={"Refunds"},
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
     *                         {"id": 1, "sale_id": 5, "warehouse_id": 1, "status": "completed"}
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
        $refunds = Refund::with('items')
            ->orderByDesc('refund_date')
            ->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $refunds]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/refunds/{refund}",
     *     summary="Get refund details",
     *     tags={"Refunds"},
     *     @OA\Parameter(
     *         name="refund",
     *         in="path",
     *         description="Refund ID",
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
     *                     "sale_id": 5,
     *                     "warehouse_id": 1,
     *                     "notes": "Customer returned defective item",
     *                     "status": "completed",
     *                     "items": {
     *                         {"id": 1, "sale_item_id": 12, "quantity": 1}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Refund not found")
     * )
     */
    public function show(Refund $refund): JsonResponse
    {
        $refund->load('items');

        return response()->json(['data' => $refund]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/refunds",
     *     summary="Create new refund",
     *     tags={"Refunds"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"sale_id", "warehouse_id", "items"},
     *             @OA\Property(property="sale_id", type="integer"),
     *             @OA\Property(property="warehouse_id", type="integer"),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="sale_item_id", type="integer"),
     *                     @OA\Property(property="quantity", type="number")
     *                 )
     *             ),
     *             @OA\Property(property="notes", type="string"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="branch_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Refund created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Refund created successfully.",
     *                 "data": {
     *                     "id": 2,
     *                     "sale_id": 8,
     *                     "warehouse_id": 2,
     *                     "status": "pending"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreRefundRequest $request): JsonResponse
    {
        $refund = $this->refundService->createRefund(
            saleId: $request->validated('sale_id'),
            warehouseId: $request->validated('warehouse_id'),
            items: $request->validated('items'),
            notes: $request->validated('notes'),
            currency: $request->validated('currency'),
            branchId: $request->validated('branch_id')
        );

        return response()->json([
            'data' => $refund,
            'message' => 'Refund created successfully.',
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/refunds/{refund}",
     *     summary="Update refund status/notes",
     *     tags={"Refunds"},
     *     @OA\Parameter(
     *         name="refund",
     *         in="path",
     *         description="Refund ID",
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
     *         description="Refund updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Refund updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "status": "completed",
     *                     "notes": "Refund processed"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Refund not found")
     * )
     */
    public function update(UpdateRefundRequest $request, Refund $refund): JsonResponse
    {
        $validated = $request->validated();

        $refund = $this->refundService->updateRefund($refund, $validated);

        return response()->json(['data' => $refund, 'message' => 'Refund updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/refunds/{refund}",
     *     summary="Delete refund record",
     *     tags={"Refunds"},
     *     @OA\Parameter(
     *         name="refund",
     *         in="path",
     *         description="Refund ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Refund deleted successfully"),
     *     @OA\Response(response=404, description="Refund not found")
     * )
     */
    public function destroy(Refund $refund): JsonResponse
    {
        $this->refundService->deleteRefund($refund);

        return response()->json(['message' => 'Refund deleted.'], 204);
    }
}
