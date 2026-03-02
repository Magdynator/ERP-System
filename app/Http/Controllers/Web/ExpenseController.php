<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Expenses\Models\Expense;
use Erp\Expenses\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function index(): View
    {
        $expenses = Expense::with('category')->orderByDesc('expense_date')->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        $categories = \Erp\Expenses\Models\ExpenseCategory::orderBy('name')->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $this->expenseService->createExpense(
            expenseCategoryId: (int) $validated['expense_category_id'],
            amount: (float) $validated['amount'],
            expenseDate: \Carbon\Carbon::parse($validated['expense_date']),
            vendorName: $validated['vendor_name'] ?? null,
            vendorReference: $validated['vendor_reference'] ?? null,
            description: $validated['description'] ?? null,
            currency: $validated['currency'] ?? 'USD',
            branchId: $validated['branch_id'] ?? null
        );

        return redirect()->route('web.expenses.index')->with('success', 'Expense created.');
    }

    public function show(Expense $expense): View
    {
        $expense->load('category');

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $categories = \Erp\Expenses\Models\ExpenseCategory::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'vendor_reference' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
        ]);
        $expense->update($validated);

        return redirect()->route('web.expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('web.expenses.index')->with('success', 'Expense deleted.');
    }
}
