<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Erp\Products\Models\Product;
use Erp\Sales\Models\Sale;
use Erp\Refunds\Models\Refund;
use Erp\Expenses\Models\Expense;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'products' => Product::count(),
            'sales' => Sale::count(),
            'refunds' => Refund::count(),
            'expenses' => Expense::count(),
            'audit_logs' => auth()->user()->isAdmin() ? \Erp\Core\Models\AuditLog::count() : 0,
        ];

        $now = Carbon::now();
        $thisMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $salesThisMonth = Sale::where('sale_date', '>=', $thisMonthStart)->count();
        $salesLastMonth = Sale::whereBetween('sale_date', [$lastMonthStart, $lastMonthEnd])->count();
        $salesPercent = $salesLastMonth > 0 ? (($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100 : ($salesThisMonth > 0 ? 100 : 0);

        $refundsThisMonth = Refund::where('refund_date', '>=', $thisMonthStart)->count();
        $refundsLastMonth = Refund::whereBetween('refund_date', [$lastMonthStart, $lastMonthEnd])->count();
        $refundsPercent = $refundsLastMonth > 0 ? (($refundsThisMonth - $refundsLastMonth) / $refundsLastMonth) * 100 : ($refundsThisMonth > 0 ? 100 : 0);

        $expensesThisMonth = Expense::where('expense_date', '>=', $thisMonthStart)->count();
        $expensesLastMonth = Expense::whereBetween('expense_date', [$lastMonthStart, $lastMonthEnd])->count();
        $expensesPercent = $expensesLastMonth > 0 ? (($expensesThisMonth - $expensesLastMonth) / $expensesLastMonth) * 100 : ($expensesThisMonth > 0 ? 100 : 0);

        $percents = [
            'sales' => round($salesPercent, 1),
            'refunds' => round($refundsPercent, 1),
            'expenses' => round($expensesPercent, 1),
        ];

        $recentSales = Sale::with('items')->orderByDesc('sale_date')->limit(5)->get();
        $recentRefunds = Refund::with('items')->orderByDesc('refund_date')->limit(5)->get();
        $recentLogs = auth()->user()->isAdmin() 
            ? \Erp\Core\Models\AuditLog::with('user')->orderByDesc('created_at')->limit(3)->get() 
            : collect();

        // Chart Data (Last 6 Months)
        $chartLabels = [];
        $revenueData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->format('M y');
            $revenueData[] = (float) \Erp\Sales\Models\SaleItem::whereHas('sale', function ($q) use ($month) {
                $q->whereYear('sale_date', $month->year)
                  ->whereMonth('sale_date', $month->month);
            })->sum(\Illuminate\Support\Facades\DB::raw('selling_price * quantity'));
                
            $expenseData[] = (float) Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
        }

        return view('dashboard', compact('stats', 'percents', 'recentSales', 'recentRefunds', 'recentLogs', 'chartLabels', 'revenueData', 'expenseData'));
    }
}
