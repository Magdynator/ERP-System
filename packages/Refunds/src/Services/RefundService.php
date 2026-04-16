<?php

declare(strict_types=1);

namespace Erp\Refunds\Services;

use Erp\Accounting\Contracts\AccountingServiceInterface;
use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Refunds\Models\Refund;
use Erp\Refunds\Models\RefundItem;
use Erp\Sales\Contracts\SaleRefundDataInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

use Illuminate\Pagination\LengthAwarePaginator;

class RefundService
{
    public function __construct(
        protected AccountingServiceInterface $accounting,
        protected InventoryServiceInterface $inventory,
        protected SaleRefundDataInterface $saleRefundData
    ) {}

    public function getPaginatedRefunds(int $perPage = 15): LengthAwarePaginator
    {
        return Refund::with('items')
            ->orderByDesc('refund_date')
            ->paginate($perPage);
    }

    public function updateRefund(Refund $refund, array $data): Refund
    {
        $refund->update($data);

        return $refund->fresh('items');
    }

    public function deleteRefund(Refund $refund): void
    {
        $refund->delete();
    }

    /**
     * Create refund: reverse accounting (revenue/cash), return stock. Never delete sale.
     *
     * @param  array<int, array{sale_item_id: int, quantity: float}>  $items
     */
    public function createRefund(
        int $saleId,
        int $warehouseId,
        array $items,
        ?string $notes = null,
        ?string $currency = null,
        ?int $branchId = null
    ): Refund {
        $saleData = $this->saleRefundData->getSaleWithItemsForRefund($saleId);

        if (! $saleData) {
            throw new InvalidArgumentException('Sale not found.');
        }

        if (empty($items)) {
            throw new InvalidArgumentException('Refund must have at least one item.');
        }

        $sale = $saleData['sale'];
        $saleItemsIndex = collect($saleData['items'])->keyBy('id');
        $currency = $currency ?? $sale['currency'];

        return DB::transaction(function () use ($sale, $saleItemsIndex, $warehouseId, $items, $notes, $currency, $branchId) {
            $refund = Refund::create([
                'refund_number' => $this->generateRefundNumber(),
                'sale_id' => $sale['id'],
                'warehouse_id' => $warehouseId,
                'branch_id' => $branchId ?? $sale['branch_id'],
                'currency' => $currency,
                'status' => 'completed',
                'notes' => $notes,
                'refund_date' => now(),
            ]);

            $totalAmount = 0.0;

            foreach ($items as $item) {
                $saleItemId = (int) $item['sale_item_id'];
                $saleItem = $saleItemsIndex->get($saleItemId);
                if (! $saleItem) {
                    throw new InvalidArgumentException("Sale item {$saleItemId} does not belong to this sale.");
                }
                $quantity = (float) $item['quantity'];

                if ($quantity <= 0 || $quantity > $saleItem['quantity']) {
                    throw new InvalidArgumentException("Invalid refund quantity for sale_item {$saleItemId}.");
                }

                $this->inventory->add(
                    $saleItem['product_id'],
                    $warehouseId,
                    $quantity,
                    'refund',
                    $refund->id
                );

                RefundItem::create([
                    'refund_id' => $refund->id,
                    'sale_item_id' => $saleItemId,
                    'product_id' => $saleItem['product_id'],
                    'quantity' => $quantity,
                    'cost_price' => $saleItem['cost_price'],
                    'selling_price' => $saleItem['selling_price'],
                ]);

                $totalAmount += $saleItem['selling_price'] * $quantity;
            }

            $revenueAccountId = $this->accounting->getAccountIdByCode('REVENUE');
            $cashAccountId = $this->accounting->getAccountIdByCode('CASH');

            if ($revenueAccountId && $cashAccountId && $totalAmount > 0) {
                $this->accounting->recordEntry(
                    'Refund #' . $refund->refund_number . ' (reversal)',
                    [
                        ['account_id' => $revenueAccountId, 'debit' => $totalAmount, 'credit' => 0],
                        ['account_id' => $cashAccountId, 'debit' => 0, 'credit' => $totalAmount],
                    ],
                    'refund',
                    $refund->id,
                    $currency,
                    $refund->branch_id
                );
            }

            return $refund->load('items');
        });
    }

    private function generateRefundNumber(): string
    {
        $prefix = 'R';
        $date = now()->format('Ymd');
        $last = Refund::withTrashed()->where('refund_number', 'like', "{$prefix}{$date}%")->count();

        return $prefix . $date . str_pad((string) ($last + 1), 4, '0', STR_PAD_LEFT);
    }
}
