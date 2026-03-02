<?php

declare(strict_types=1);

namespace Erp\Core\Services;

/**
 * Pure calculation service. Does NOT access any models.
 * Gross Profit = SUM((selling_price - cost_price) * quantity)
 * Net Profit = Gross Profit - SUM(expenses)
 */
class ProfitService
{
    /**
     * @param  array<int, array{selling_price: float, cost_price: float, quantity: float}>  $lineItems
     */
    public function calculateGrossProfit(array $lineItems): float
    {
        $total = 0.0;

        foreach ($lineItems as $item) {
            $sellingPrice = (float) ($item['selling_price'] ?? 0);
            $costPrice = (float) ($item['cost_price'] ?? 0);
            $quantity = (float) ($item['quantity'] ?? 0);
            $total += ($sellingPrice - $costPrice) * $quantity;
        }

        return $total;
    }

    public function calculateNetProfit(float $grossProfit, float $totalExpenses): float
    {
        return $grossProfit - $totalExpenses;
    }
}
