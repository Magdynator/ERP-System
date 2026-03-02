<?php

declare(strict_types=1);

namespace Tests\Feature;

use Erp\Accounting\Models\Account;
use Erp\Accounting\Models\JournalEntry;
use Erp\Inventory\Models\StockMovement;
use Erp\Inventory\Models\Warehouse;
use Erp\Products\Models\Product;
use Erp\Sales\Models\Sale;
use Erp\Sales\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAccountingAndInventory();
    }

    public function test_sale_creates_inventory_deduction_and_accounting_entry(): void
    {
        $warehouse = Warehouse::first();
        $product = Product::first();

        $saleService = app(SaleService::class);
        $sale = $saleService->createSale(
            warehouseId: $warehouse->id,
            items: [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
            payments: [
                ['amount' => 150, 'method' => 'cash'],
            ]
        );

        $this->assertInstanceOf(Sale::class, $sale);
        $this->assertCount(1, $sale->items);
        $this->assertEquals(2, $sale->items->first()->quantity);
        $this->assertEquals(75.0, (float) $sale->items->first()->selling_price);

        $outMovements = StockMovement::where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->where('type', StockMovement::TYPE_OUT)
            ->get();
        $this->assertCount(1, $outMovements);
        $this->assertEquals(2, (float) $outMovements->first()->quantity);

        $entry = JournalEntry::where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->first();
        $this->assertNotNull($entry);
        $this->assertEquals(150.0, $entry->total_debits);
        $this->assertEquals(150.0, $entry->total_credits);
    }

    private function seedAccountingAndInventory(): void
    {
        Account::create(['name' => 'Cash', 'code' => 'CASH', 'type' => 'asset', 'is_active' => true]);
        Account::create(['name' => 'Revenue', 'code' => 'REVENUE', 'type' => 'revenue', 'is_active' => true]);
        $cat = \Erp\Products\Models\Category::create(['name' => 'Gen', 'slug' => 'gen', 'is_active' => true]);
        Product::create([
            'name' => 'Test', 'sku' => 'TEST1', 'cost_price' => 50, 'selling_price' => 75,
            'tax_percentage' => 0, 'category_id' => $cat->id, 'is_active' => true,
        ]);
        $wh = Warehouse::create(['name' => 'WH1', 'code' => 'WH1', 'is_active' => true]);
        StockMovement::create([
            'product_id' => Product::first()->id,
            'warehouse_id' => $wh->id,
            'quantity' => 100,
            'type' => StockMovement::TYPE_IN,
            'reference_type' => 'opening',
            'reference_id' => 0,
        ]);
    }
}
