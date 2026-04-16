<?php

declare(strict_types=1);

namespace Erp\Accounting\Http\Controllers\Web;

use Erp\Accounting\Http\Controllers\Controller;
use Erp\Accounting\Contracts\AccountServiceInterface;
use Erp\Accounting\Http\Requests\StoreAccountRequest;
use Erp\Accounting\Http\Requests\UpdateAccountRequest;
use Erp\Accounting\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected AccountServiceInterface $accountService
    ) {}

    public function index(Request $request): View
    {
        $accounts = $this->accountService->getPaginatedAccounts(
            15,
            $request->type ?: null,
            false
        )->withQueryString();

        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->accountService->createAccount($validated);

        return redirect()->route('web.accounts.index')->with('success', 'Account created.');
    }

    public function edit(Account $account): View
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $validated = $request->validated();

        $this->accountService->updateAccount($account, $validated);

        return redirect()->route('web.accounts.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->accountService->deleteAccount($account);

        return redirect()->route('web.accounts.index')->with('success', 'Account deleted.');
    }
}
