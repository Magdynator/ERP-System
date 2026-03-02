<?php

declare(strict_types=1);

namespace Erp\Products\Database\Seeders;

use Erp\Products\Models\Category;
use Erp\Products\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::where('slug', 'general')->first();

        Product::firstOrCreate(
            ['sku' => 'SKU001'],
            [
                'name' => 'Sample Product',
                'cost_price' => 50.00,
                'selling_price' => 75.00,
                'tax_percentage' => 0,
                'category_id' => $category?->id,
                'is_active' => true,
            ]
        );
    }
}
