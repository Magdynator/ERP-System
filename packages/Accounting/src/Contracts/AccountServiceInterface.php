<?php

declare(strict_types=1);

namespace Erp\Accounting\Contracts;

use Erp\Accounting\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AccountServiceInterface
{
    public function getPaginatedAccounts(int $perPage = 15, ?string $type = null, bool $activeOnly = false): LengthAwarePaginator;
    public function createAccount(array $data): Account;
    public function updateAccount(Account $account, array $data): Account;
    public function deleteAccount(Account $account): void;
    public function getAccountBalance(int $accountId): float;
    public function getActiveAccounts(): Collection;
}
