<?php

declare(strict_types=1);

namespace Erp\Inventory\Database\Seeders;

use Erp\Inventory\Models\StockMovement;
use Erp\Inventory\Models\Warehouse;
use Erp\Products\Models\Product;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::where('code', 'WH01')->first();
        $product = Product::where('sku', 'SKU001')->first();

        if (! $warehouse || ! $product) {
            return;
        }

        StockMovement::firstOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'reference_type' => 'opening',
                'reference_id' => 0,
            ],
            [
                'quantity' => 100,
                'type' => StockMovement::TYPE_IN,
            ]
        );
    }
}
