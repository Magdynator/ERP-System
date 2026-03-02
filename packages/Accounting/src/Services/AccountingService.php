<?php

declare(strict_types=1);

namespace Erp\Accounting\Services;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\Account;
use Erp\Accounting\Models\JournalEntry;
use Erp\Accounting\Models\JournalLine;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AccountingService implements AccountingServiceInterface
{
    public function recordEntry(
        string $description,
        array $lines,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $currency = null,
        ?int $branchId = null
    ): object {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($lines as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        if (abs($totalDebit - $totalCredit) > 0.0001) {
            throw new InvalidArgumentException(
                'Journal entry must balance. Debits: ' . $totalDebit . ', Credits: ' . $totalCredit
            );
        }

        return DB::transaction(function () use ($description, $lines, $referenceType, $referenceId, $currency, $branchId) {
            $entry = JournalEntry::create([
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'currency' => $currency ?? config('core.currency', 'USD'),
                'branch_id' => $branchId,
                'entry_date' => now(),
            ]);

            foreach ($lines as $line) {
                JournalLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                ]);
            }

            return $entry;
        });
    }

    public function getAccountBalance(int $accountId, ?int $branchId = null): float
    {
        $query = JournalLine::query()
            ->where('account_id', $accountId)
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_lines.journal_entry_id');

        if ($branchId !== null) {
            $query->where('journal_entries.branch_id', $branchId);
        }

        $totals = $query->selectRaw('SUM(journal_lines.debit) as total_debit, SUM(journal_lines.credit) as total_credit')
            ->first();

        $debit = (float) ($totals->total_debit ?? 0);
        $credit = (float) ($totals->total_credit ?? 0);

        return $debit - $credit;
    }

    public function reverseEntry(int $journalEntryId): object
    {
        $original = JournalEntry::with('lines')->findOrFail($journalEntryId);

        $reversalLines = [];
        foreach ($original->lines as $line) {
            $reversalLines[] = [
                'account_id' => $line->account_id,
                'debit' => $line->credit,
                'credit' => $line->debit,
            ];
        }

        return $this->recordEntry(
            'Reversal: ' . $original->description,
            $reversalLines,
            $original->reference_type,
            $original->reference_id,
            $original->currency,
            $original->branch_id
        );
    }

    public function getAccountIdByCode(string $code): ?int
    {
        $account = Account::where('code', $code)->where('is_active', true)->first();

        return $account?->id;
    }
}
