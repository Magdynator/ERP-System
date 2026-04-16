<?php

declare(strict_types=1);

namespace Erp\Expenses\Services;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Expenses\Models\Expense;
use Illuminate\Support\Facades\DB;

use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseService
{
    public function __construct(
        protected AccountingServiceInterface $accounting
    ) {}

    public function getPaginatedExpenses(int $perPage = 15): LengthAwarePaginator
    {
        return Expense::with('category')
            ->orderByDesc('expense_date')
            ->paginate($perPage);
    }

    public function updateExpense(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense->fresh('category');
    }

    public function deleteExpense(Expense $expense): void
    {
        $expense->delete();
    }

    /**
     * Create expense and record journal entry: Dr Expense, Cr Cash.
     */
    public function createExpense(
        int $expenseCategoryId,
        float $amount,
        \DateTimeInterface $expenseDate,
        ?string $vendorName = null,
        ?string $vendorReference = null,
        ?string $description = null,
        ?string $currency = null,
        ?int $branchId = null
    ): Expense {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Expense amount must be positive.');
        }

        $currency = $currency ?? config('core.currency', 'USD');

        return DB::transaction(function () use ($expenseCategoryId, $amount, $expenseDate, $vendorName, $vendorReference, $description, $currency, $branchId) {
            $expense = Expense::create([
                'expense_category_id' => $expenseCategoryId,
                'amount' => $amount,
                'currency' => $currency,
                'expense_date' => $expenseDate,
                'vendor_name' => $vendorName,
                'vendor_reference' => $vendorReference,
                'description' => $description,
                'branch_id' => $branchId,
            ]);

            $expenseAccountId = $this->accounting->getAccountIdByCode('EXPENSE');
            $cashAccountId = $this->accounting->getAccountIdByCode('CASH');

            if ($expenseAccountId && $cashAccountId) {
                $this->accounting->recordEntry(
                    'Expense: ' . ($description ?? 'Expense #' . $expense->id),
                    [
                        ['account_id' => $expenseAccountId, 'debit' => $amount, 'credit' => 0],
                        ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $amount],
                    ],
                    'expense',
                    $expense->id,
                    $currency,
                    $branchId
                );
            }

            return $expense;
        });
    }
}
