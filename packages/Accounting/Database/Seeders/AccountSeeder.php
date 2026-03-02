<?php

declare(strict_types=1);

namespace Erp\Accounting\Database\Seeders;

use Erp\Accounting\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Cash', 'code' => 'CASH', 'type' => 'asset'],
            ['name' => 'Accounts Receivable', 'code' => 'RECEIVABLE', 'type' => 'asset'],
            ['name' => 'Sales Revenue', 'code' => 'REVENUE', 'type' => 'revenue'],
            ['name' => 'Expenses', 'code' => 'EXPENSE', 'type' => 'expense'],
        ];

        foreach ($accounts as $data) {
            Account::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'is_active' => true,
                ]
            );
        }
    }
}
