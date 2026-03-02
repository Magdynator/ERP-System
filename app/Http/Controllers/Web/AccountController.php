<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected AccountingServiceInterface $accounting
    ) {}

    public function index(Request $request): View
    {
        $query = Account::query();
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $accounts = $query->orderBy('code')->paginate(15)->withQueryString();

        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:accounts,code'],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);

        Account::create($validated);

        return redirect()->route('web.accounts.index')->with('success', 'Account created.');
    }

    public function edit(Account $account): View
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:accounts,code,' . $account->id],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);

        $account->update($validated);

        return redirect()->route('web.accounts.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $account->delete();

        return redirect()->route('web.accounts.index')->with('success', 'Account deleted.');
    }
}
