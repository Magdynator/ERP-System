<?php

declare(strict_types=1);

namespace Erp\Sales\Services;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Products\Contracts\ProductServiceInterface;
use Erp\Sales\Models\Payment;
use Erp\Sales\Models\Sale;
use Erp\Sales\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

use Illuminate\Pagination\LengthAwarePaginator;

class SaleService
{
    public function __construct(
        protected AccountingServiceInterface $accounting,
        protected InventoryServiceInterface $inventory,
        protected ProductServiceInterface $productService
    ) {}

    public function getPaginatedSales(int $perPage = 15): LengthAwarePaginator
    {
        return Sale::with(['items', 'payments'])
            ->orderByDesc('sale_date')
            ->paginate($perPage);
    }

    public function updateSale(Sale $sale, array $data): Sale
    {
        $sale->update($data);

        return $sale->fresh(['items', 'payments']);
    }

    public function deleteSale(Sale $sale): void
    {
        $sale->delete();
    }

    /**
     * Create a sale: snapshot prices, deduct inventory, record accounting entry.
     *
     * @param  array<int, array{product_id: int, quantity: float}>  $items
     * @param  array<int, array{amount: float, method: string, reference?: string}>  $payments
     */
    public function createSale(
        int $warehouseId,
        array $items,
        array $payments = [],
        ?string $customerName = null,
        ?string $customerEmail = null,
        ?string $currency = null,
        ?int $branchId = null
    ): Sale {
        if (empty($items)) {
            throw new InvalidArgumentException('Sale must have at least one item.');
        }

        $currency = $currency ?? config('core.currency', 'USD');

        return DB::transaction(function () use ($warehouseId, $items, $payments, $customerName, $customerEmail, $currency, $branchId) {
            $sale = Sale::create([
                'sale_number' => $this->generateSaleNumber(),
                'warehouse_id' => $warehouseId,
                'branch_id' => $branchId,
                'currency' => $currency,
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'status' => 'completed',
                'sale_date' => now(),
            ]);

            $totalAmount = 0.0;
            $lines = [];

            foreach ($items as $item) {
                $productData = $this->productService->getForSale((int) $item['product_id']);
                if (! $productData) {
                    throw new InvalidArgumentException('Product not found or inactive: ' . $item['product_id']);
                }
                $quantity = (float) $item['quantity'];

                if ($quantity <= 0) {
                    throw new InvalidArgumentException("Invalid quantity for product {$productData['id']}.");
                }

                $this->inventory->deduct(
                    $productData['id'],
                    $warehouseId,
                    $quantity,
                    'sale',
                    $sale->id
                );

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productData['id'],
                    'quantity' => $quantity,
                    'cost_price' => $productData['cost_price'],
                    'selling_price' => $productData['selling_price'],
                    'tax_percentage' => $productData['tax_percentage'],
                ]);

                $lineTotal = $productData['selling_price'] * $quantity;
                $totalAmount += $lineTotal;
            }

            foreach ($payments as $payment) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $payment['amount'],
                    'method' => $payment['method'],
                    'reference' => $payment['reference'] ?? null,
                    'paid_at' => now(),
                ]);
            }

            $revenueAccountId = $this->accounting->getAccountIdByCode('REVENUE');
            $cashAccountId = $this->accounting->getAccountIdByCode('CASH');

            if ($revenueAccountId && $cashAccountId && $totalAmount > 0) {
                $this->accounting->recordEntry(
                    'Sale #' . $sale->sale_number,
                    [
                        ['account_id' => $cashAccountId, 'debit' => $totalAmount, 'credit' => 0],
                        ['account_id' => $revenueAccountId, 'debit' => 0, 'credit' => $totalAmount],
                    ],
                    'sale',
                    $sale->id,
                    $currency,
                    $branchId
                );
            }

            return $sale->load(['items', 'payments']);
        });
    }

    private function generateSaleNumber(): string
    {
        $prefix = 'S';
        $date = now()->format('Ymd');
        $last = Sale::withTrashed()->where('sale_number', 'like', "{$prefix}{$date}%")->count();

        return $prefix . $date . str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
