<?php

declare(strict_types=1);

namespace Erp\Accounting\Contracts;

interface AccountingServiceInterface
{
    /**
     * Record a journal entry with balanced debit/credit lines.
     *
     * @param  array<int, array{account_id: int, debit: float, credit: float}>  $lines
     */
    public function recordEntry(
        string $description,
        array $lines,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $currency = null,
        ?int $branchId = null
    ): object;

    /**
     * Get balance for an account (debits - credits).
     */
    public function getAccountBalance(int $accountId, ?int $branchId = null): float;

    /**
     * Create a reversal entry for a given journal entry.
     */
    public function reverseEntry(int $journalEntryId): object;

    /**
     * Get account ID by code (e.g. REVENUE, CASH, RECEIVABLE, EXPENSE).
     */
    public function getAccountIdByCode(string $code): ?int;
}
