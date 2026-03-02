<?php

declare(strict_types=1);

namespace Database\Seeders;

use Erp\Accounting\Database\Seeders\AccountSeeder;
use Erp\Core\Models\User;
use Erp\Expenses\Database\Seeders\ExpenseCategorySeeder;
use Erp\Inventory\Database\Seeders\StockMovementSeeder;
use Erp\Inventory\Database\Seeders\WarehouseSeeder;
use Erp\Products\Database\Seeders\CategorySeeder;
use Erp\Products\Database\Seeders\ProductSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@erp.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'manager@erp.test'],
            [
                'name' => 'Admin Manager',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $regularUser = User::firstOrCreate(
            ['email' => 'user@erp.test'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call([
            AccountSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            WarehouseSeeder::class,
            StockMovementSeeder::class,
            ExpenseCategorySeeder::class,
        ]);
    }
}
