<?php

declare(strict_types=1);

namespace Erp\Accounting\Services;

use Erp\Accounting\Contracts\AccountServiceInterface;
use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Accounting\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AccountService implements AccountServiceInterface
{
    public function __construct(
        protected AccountingServiceInterface $accountingService
    ) {}

    public function getPaginatedAccounts(int $perPage = 15, ?string $type = null, bool $activeOnly = false): LengthAwarePaginator
    {
        $cacheKey = "accounts_page_{$perPage}_type_{$type}_active_{$activeOnly}_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 3600, function () use ($perPage, $type, $activeOnly) {
            $query = Account::query();
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            if ($type) {
                $query->where('type', $type);
            }

            return $query->orderBy('code')->paginate($perPage);
        });
    }

    public function getActiveAccounts(): Collection
    {
        return Account::where('is_active', true)->orderBy('code')->get();
    }

    public function createAccount(array $data): Account
    {
        $data['is_active'] = $data['is_active'] ?? true;
        $account = Account::create($data);
        $this->clearCache();

        return $account;
    }

    public function updateAccount(Account $account, array $data): Account
    {
        $account->update($data);
        $this->clearCache();

        return $account->fresh();
    }

    public function deleteAccount(Account $account): void
    {
        $account->delete();
        $this->clearCache();
    }

    public function getAccountBalance(int $accountId): float
    {
        return $this->accountingService->getAccountBalance($accountId);
    }

    protected function clearCache(): void
    {
        Cache::flush(); // Or use tags to flush specific accounts cache. For simplicity, flushing tags or redis if configured.
    }
}
