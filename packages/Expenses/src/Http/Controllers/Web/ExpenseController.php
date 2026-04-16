<?php

declare(strict_types=1);

namespace Erp\Expenses\Http\Controllers\Web;

use Erp\Expenses\Http\Requests\StoreExpenseRequest;
use Erp\Expenses\Http\Requests\UpdateExpenseRequest;
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
        $expenses = $this->expenseService->getPaginatedExpenses(15);

        return view('expenses.index', compact('expenses'));
    }

    public function create(): View
    {
        $categories = \Erp\Expenses\Models\ExpenseCategory::orderBy('name')->get();

        return view('expenses.create', compact('categories'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validated();
        $this->expenseService->updateExpense($expense, $validated);

        return redirect()->route('web.expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->expenseService->deleteExpense($expense);

        return redirect()->route('web.expenses.index')->with('success', 'Expense deleted.');
    }
}
