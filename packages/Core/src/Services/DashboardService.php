<?php

declare(strict_types=1);

namespace Erp\Core\Services;

use Erp\Core\Models\AuditLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'products'   => \Erp\Products\Models\Product::count(),
            'sales'      => \Erp\Sales\Models\Sale::count(),
            'refunds'    => \Erp\Refunds\Models\Refund::count(),
            'expenses'   => \Erp\Expenses\Models\Expense::count(),
            'audit_logs' => auth()->user()->isAdmin() ? AuditLog::count() : 0,
        ];
    }

    public function getMonthlyPercents(): array
    {
        $now            = Carbon::now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd   = $now->copy()->subMonth()->endOfMonth();

        $salesThisMonth = \Erp\Sales\Models\Sale::where('sale_date', '>=', $thisMonthStart)->count();
        $salesLastMonth = \Erp\Sales\Models\Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->count();
        $salesPercent   = $salesLastMonth > 0
            ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100
            : ($salesThisMonth > 0 ? 100 : 0);

        $refundsThisMonth = \Erp\Refunds\Models\Refund::where('refund_date', '>=', $thisMonthStart)->count();
        $refundsLastMonth = \Erp\Refunds\Models\Refund::whereBetween('refund_date', [$lastMonthStart, $lastMonthEnd])->count();
        $refundsPercent   = $refundsLastMonth > 0
            ? (($refundsThisMonth - $refundsLastMonth) / $refundsLastMonth) * 100
            : ($refundsThisMonth > 0 ? 100 : 0);

        $expensesThisMonth = \Erp\Expenses\Models\Expense::where('expense_date', '>=', $thisMonthStart)->count();
        $expensesLastMonth = \Erp\Expenses\Models\Expense::whereBetween('expense_date', [$lastMonthStart, $lastMonthEnd])->count();
        $expensesPercent   = $expensesLastMonth > 0
            ? (($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100
            : ($expensesThisMonth > 0 ? 100 : 0);

        return [
            'sales'    => round($salesPercent, 1),
            'refunds'  => round($refundsPercent, 1),
            'expenses' => round($expensesPercent, 1),
        ];
    }

    public function getRecentActivity(): array
    {
        return [
            'recentSales'   => \Erp\Sales\Models\Sale::with('items')->orderByDesc('sale_date')->limit(5)->get(),
            'recentRefunds' => \Erp\Refunds\Models\Refund::with('items')->orderByDesc('refund_date')->limit(5)->get(),
            'recentLogs'    => auth()->user()->isAdmin()
                ? AuditLog::with('user')->orderByDesc('created_at')->limit(3)->get()
                : collect(),
        ];
    }

    public function getChartData(): array
    {
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month         = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->format('M y');

            $revenueData[] = (float) \Erp\Sales\Models\SaleItem::whereHas('sale', function ($q) use ($month) {
                $q->whereYear('sale_date', $month->year)
                  ->whereMonth('sale_date', $month->month);
            })->sum(DB::raw('selling_price * quantity'));

            $expenseData[] = (float) \Erp\Expenses\Models\Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
        }

        return compact('chartLabels', 'revenueData', 'expenseData');
    }
}
