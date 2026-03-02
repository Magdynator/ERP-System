<?php

declare(strict_types=1);

namespace Erp\Accounting\Http\Controllers;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        protected AccountingServiceInterface $accountingService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/accounts",
     *     summary="List all ledger accounts",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by account type (asset, liability, equity, revenue, expense)",
     *         required=false,
     *         @OA\Schema(type="string")
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
     *                         {"id": 1, "name": "Cash equivalent", "code": "1000", "type": "asset", "is_active": true}
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
        $query = Account::query();
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        $accounts = $query->orderBy('code')->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $accounts]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/accounts/{account}",
     *     summary="Get account details with balance",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="account",
     *         in="path",
     *         description="Account ID",
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
     *                     "name": "Cash equivalent",
     *                     "code": "1000",
     *                     "type": "asset",
     *                     "is_active": true,
     *                     "balance": 15000.50
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Account not found")
     * )
     */
    public function show(Account $account): JsonResponse
    {
        $balance = $this->accountingService->getAccountBalance($account->id);
        $account->setAttribute('balance', $balance);

        return response()->json(['data' => $account]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/accounts",
     *     summary="Create new ledger account",
     *     tags={"Accounting"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "type"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="type", type="string", enum={"asset", "liability", "equity", "revenue", "expense"}),
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Account created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Account created.",
     *                 "data": {
     *                     "id": 2,
     *                     "name": "Accounts Receivable",
     *                     "code": "1200",
     *                     "type": "asset",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:accounts,code'],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $account = Account::create($validated);

        return response()->json(['data' => $account, 'message' => 'Account created.'], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/accounts/{account}",
     *     summary="Update existing ledger account",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="account",
     *         in="path",
     *         description="Account ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="type", type="string", enum={"asset", "liability", "equity", "revenue", "expense"}),
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Account updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Account updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "name": "Primary Cash",
     *                     "code": "1000",
     *                     "type": "asset",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Account not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Account $account): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['sometimes', 'string', 'max:255', 'unique:accounts,code,' . $account->id],
            'type' => ['sometimes', 'string', 'in:asset,liability,equity,revenue,expense'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $account->update($validated);

        return response()->json(['data' => $account->fresh(), 'message' => 'Account updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/accounts/{account}",
     *     summary="Delete ledger account",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="account",
     *         in="path",
     *         description="Account ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Account deleted successfully"),
     *     @OA\Response(response=404, description="Account not found")
     * )
     */
    public function destroy(Account $account): JsonResponse
    {
        $account->delete();

        return response()->json(['message' => 'Account deleted.'], 204);
    }
}
