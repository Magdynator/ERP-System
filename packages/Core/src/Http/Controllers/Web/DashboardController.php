<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers\Web;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function __invoke(): View
    {
        $stats    = $this->dashboardService->getStats();
        $percents = $this->dashboardService->getMonthlyPercents();
        $activity = $this->dashboardService->getRecentActivity();
        $chart    = $this->dashboardService->getChartData();

        return view('dashboard', [
            'stats'         => $stats,
            'percents'      => $percents,
            'recentSales'   => $activity['recentSales'],
            'recentRefunds' => $activity['recentRefunds'],
            'recentLogs'    => $activity['recentLogs'],
            'chartLabels'   => $chart['chartLabels'],
            'revenueData'   => $chart['revenueData'],
            'expenseData'   => $chart['expenseData'],
        ]);
    }
}
