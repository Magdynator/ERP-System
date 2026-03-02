<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\JournalEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function __construct(
        protected AccountingServiceInterface $accounting
    ) {}

    public function index(): View
    {
        $entries = JournalEntry::with('lines.account')
            ->orderByDesc('entry_date')
            ->paginate(15);

        return view('journal-entries.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = \Erp\Accounting\Models\Account::where('is_active', true)->orderBy('code')->get();

        return view('journal-entries.create', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'branch_id' => ['nullable', 'integer'],
        ]);

        $lines = [];
        foreach ($validated['lines'] as $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);
            if ($debit > 0 || $credit > 0) {
                $lines[] = [
                    'account_id' => (int) $line['account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                ];
            }
        }

        if (count($lines) < 2) {
            return redirect()->back()->withInput()->with('error', 'At least 2 lines with debit or credit required.');
        }

        $this->accounting->recordEntry(
            $validated['description'],
            $lines,
            null,
            null,
            $validated['currency'] ?? null,
            $validated['branch_id'] ?? null
        );

        return redirect()->route('web.journal-entries.index')->with('success', 'Journal entry created.');
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load('lines.account');

        return view('journal-entries.show', compact('journalEntry'));
    }
}
