<?php

declare(strict_types=1);

namespace Erp\Accounting\Http\Controllers;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\JournalEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct(
        protected AccountingServiceInterface $accountingService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/v1/journal-entries",
     *     summary="List all journal entries",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="account_id",
     *         in="query",
     *         description="Filter by associated account ID",
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
     *                             "id": 1, 
     *                             "entry_date": "2023-10-27", 
     *                             "description": "Initial capital",
     *                             "lines": {
     *                                 {"account_id": 1, "debit": 10000, "credit": 0}
     *                             }
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
        $query = JournalEntry::query()->with('lines.account');
        if ($request->filled('account_id')) {
            $query->whereHas('lines', fn ($q) => $q->where('account_id', $request->integer('account_id')));
        }
        $entries = $query->orderByDesc('entry_date')->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $entries]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/journal-entries/{journalEntry}",
     *     summary="Get journal entry details",
     *     tags={"Accounting"},
     *     @OA\Parameter(
     *         name="journalEntry",
     *         in="path",
     *         description="Journal Entry ID",
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
     *                     "entry_date": "2023-10-27", 
     *                     "description": "Initial capital",
     *                     "currency": "USD",
     *                     "lines": {
     *                         {"id": 1, "account_id": 1, "debit": 10000, "credit": 0, "account": {"name": "Primary Cash"}},
     *                         {"id": 2, "account_id": 3, "debit": 0, "credit": 10000, "account": {"name": "Owner Equity"}}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Journal Entry not found")
     * )
     */
    public function show(JournalEntry $journalEntry): JsonResponse
    {
        $journalEntry->load('lines.account');

        return response()->json(['data' => $journalEntry]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/journal-entries",
     *     summary="Record a new journal entry",
     *     tags={"Accounting"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"description", "lines"},
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="reference_type", type="string"),
     *             @OA\Property(property="reference_id", type="integer"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="branch_id", type="integer"),
     *             @OA\Property(
     *                 property="lines",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="account_id", type="integer"),
     *                     @OA\Property(property="debit", type="number"),
     *                     @OA\Property(property="credit", type="number")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Journal entry recorded successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Journal entry created.",
     *                 "data": {
     *                     "id": 2,
     *                     "entry_date": "2023-10-28",
     *                     "description": "Office Supplies",
     *                     "lines": {
     *                         {"account_id": 5, "debit": 150, "credit": 0},
     *                         {"account_id": 1, "debit": 0, "credit": 150}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error or unbalanced entry")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines.*.debit' => ['required_without:lines.*.credit', 'numeric', 'min:0'],
            'lines.*.credit' => ['required_without:lines.*.debit', 'numeric', 'min:0'],
            'reference_type' => ['nullable', 'string'],
            'reference_id' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'max:3'],
            'branch_id' => ['nullable', 'integer'],
        ]);
        foreach ($validated['lines'] as $line) {
            $line['debit'] = (float) ($line['debit'] ?? 0);
            $line['credit'] = (float) ($line['credit'] ?? 0);
        }

        $entry = $this->accountingService->recordEntry(
            $validated['description'],
            $validated['lines'],
            $validated['reference_type'] ?? null,
            $validated['reference_id'] ?? null,
            $validated['currency'] ?? null,
            $validated['branch_id'] ?? null
        );

        return response()->json(['data' => $entry->load('lines.account'), 'message' => 'Journal entry created.'], 201);
    }
}
