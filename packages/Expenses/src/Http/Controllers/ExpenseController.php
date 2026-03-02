<?php

declare(strict_types=1);

namespace Erp\Expenses\Http\Controllers;

use Erp\Expenses\Http\Requests\StoreExpenseRequest;
use Erp\Expenses\Models\Expense;
use Erp\Expenses\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/expenses",
     *     summary="List all expenses",
     *     tags={"Expenses"},
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
     *                         {"id": 1, "amount": 500.00, "expense_date": "2023-11-01", "vendor_name": "Tech Corp", "description": "Server Hosting"}
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
        $expenses = Expense::with('category')
            ->orderByDesc('expense_date')
            ->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $expenses]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/expenses/{expense}",
     *     summary="Get expense details",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         description="Expense ID",
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
     *                     "amount": 500.00,
     *                     "expense_date": "2023-11-01",
     *                     "vendor_name": "Tech Corp",
     *                     "description": "Server Hosting",
     *                     "currency": "USD"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function show(Expense $expense): JsonResponse
    {
        $expense->load('category');

        return response()->json(['data' => $expense]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/expenses",
     *     summary="Create new expense",
     *     tags={"Expenses"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"expense_category_id", "amount", "expense_date"},
     *             @OA\Property(property="expense_category_id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="expense_date", type="string", format="date"),
     *             @OA\Property(property="vendor_name", type="string"),
     *             @OA\Property(property="vendor_reference", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="branch_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Expense created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Expense created successfully.",
     *                 "data": {
     *                     "id": 2,
     *                     "amount": 120.50,
     *                     "expense_date": "2023-11-02",
     *                     "vendor_name": "Office Depot"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->createExpense(
            expenseCategoryId: $request->validated('expense_category_id'),
            amount: (float) $request->validated('amount'),
            expenseDate: $request->validated('expense_date'),
            vendorName: $request->validated('vendor_name'),
            vendorReference: $request->validated('vendor_reference'),
            description: $request->validated('description'),
            currency: $request->validated('currency'),
            branchId: $request->validated('branch_id')
        );

        return response()->json([
            'data' => $expense,
            'message' => 'Expense created successfully.',
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/expenses/{expense}",
     *     summary="Update existing expense",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         description="Expense ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="expense_category_id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="expense_date", type="string", format="date"),
     *             @OA\Property(property="vendor_name", type="string"),
     *             @OA\Property(property="vendor_reference", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="branch_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Expense updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "amount": 550.00,
     *                     "description": "Upgraded Server Hosting"
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Expense not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Expense $expense): JsonResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['sometimes', 'integer', 'exists:expense_categories,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'expense_date' => ['sometimes', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $expense->update($validated);

        return response()->json(['data' => $expense->fresh('category'), 'message' => 'Expense updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/expenses/{expense}",
     *     summary="Delete expense",
     *     tags={"Expenses"},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         description="Expense ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Expense deleted successfully"),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json(['message' => 'Expense deleted.'], 204);
    }
}
