<?php

declare(strict_types=1);

namespace Erp\Inventory\Database\Seeders;

use Erp\Inventory\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::firstOrCreate(
            ['code' => 'WH01'],
            ['name' => 'Main Warehouse', 'is_active' => true]
        );
    }
}
