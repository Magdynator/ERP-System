<?php

declare(strict_types=1);

namespace Erp\Expenses\Database\Seeders;

use Erp\Expenses\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        ExpenseCategory::firstOrCreate(
            ['code' => 'GEN'],
            ['name' => 'General', 'is_active' => true]
        );
    }
}
