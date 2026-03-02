<?php

declare(strict_types=1);

namespace Erp\Products\Database\Seeders;

use Erp\Products\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'is_active' => true]
        );
    }
}
