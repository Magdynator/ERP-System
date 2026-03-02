<?php

declare(strict_types=1);

namespace Erp\Sales\Services;

use Erp\Sales\Contracts\SaleRefundDataInterface;
use Erp\Sales\Models\Sale;

class SaleRefundDataService implements SaleRefundDataInterface
{
    public function getSaleWithItemsForRefund(int $saleId): ?array
    {
        $sale = Sale::with('items')->find($saleId);

        if (! $sale) {
            return null;
        }

        $items = [];
        foreach ($sale->items as $item) {
            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
                'selling_price' => (float) $item->selling_price,
                'cost_price' => (float) $item->cost_price,
            ];
        }

        return [
            'sale' => [
                'id' => $sale->id,
                'currency' => $sale->currency,
                'branch_id' => $sale->branch_id,
            ],
            'items' => $items,
        ];
    }
}
