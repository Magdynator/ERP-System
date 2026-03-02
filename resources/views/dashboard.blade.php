@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')

{{-- ── Greeting Header ── --}}
<header class="mb-10 reveal">
    @php
        $hour = (int) now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    @endphp
    <h1 class="heading-1 translate-y-0 opacity-100 transition-all duration-700">
        {{ $greeting }}, <span class="bg-gradient-to-r from-brand-500 to-brand-700 bg-clip-text text-transparent">{{ auth()->user()->name ?? 'Command' }}</span>
    </h1>
    <p class="text-lg text-gray-500 mt-3 font-medium tracking-tight">System overview and real-time operational status.</p>
</header>

{{-- ── Stats Architecture ── --}}
<section class="mb-10 py-4 -mx-4 px-4 overflow-x-auto no-scrollbar">
    <div class="grid grid-cols-2 lg:grid-cols-4 {{ auth()->user()->isAdmin() ? 'xl:grid-cols-5' : '' }} gap-5">
        {{-- Products --}}
        <div class="stat-card-modern group hover:scale-[1.02] active:scale-[0.98]">
            <div class="flex items-center justify-between mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Inventory Units</span>
                <div class="stat-icon bg-brand-50 text-brand-500 shadow-inner group-hover:rotate-12 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                </div>
            </div>
            <span class="stat-value-modern" data-count-target="{{ $stats['products'] ?? 0 }}">0</span>
            <div class="h-1 bg-gray-100 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-brand-500 w-2/3"></div>
            </div>
        </div>

        {{-- Sales --}}
        <div class="stat-card-modern group hover:scale-[1.02] active:scale-[0.98]">
            <div class="flex items-center justify-between mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Performance</span>
                <div class="stat-icon bg-emerald-50 text-emerald-500 shadow-inner group-hover:-rotate-12 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                </div>
            </div>
            <span class="stat-value-modern" data-count-target="{{ $stats['sales'] ?? 0 }}">0</span>
            @php $saleTrend = $percents['sales'] ?? 0; @endphp
            <div class="flex items-center gap-2 mt-4">
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $saleTrend >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    {{ $saleTrend >= 0 ? '+' : '' }}{{ $saleTrend }}%
                </span>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Growth</span>
            </div>
        </div>

        {{-- Refunds --}}
        <div class="stat-card-modern group hover:scale-[1.02] active:scale-[0.98]">
            <div class="flex items-center justify-between mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Returns</span>
                <div class="stat-icon bg-amber-50 text-amber-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                </div>
            </div>
            <span class="stat-value-modern" data-count-target="{{ $stats['refunds'] ?? 0 }}">0</span>
            <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                System Managed
            </div>
        </div>

        {{-- Expenses --}}
        <div class="stat-card-modern group hover:scale-[1.02] active:scale-[0.98]">
            <div class="flex items-center justify-between mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Outflow</span>
                <div class="stat-icon bg-rose-50 text-rose-500">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                </div>
            </div>
            <span class="stat-value-modern" data-count-target="{{ $stats['expenses'] ?? 0 }}">0</span>
            <div class="flex items-center gap-2 mt-4">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Active Tracking</span>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        {{-- Audit Logs --}}
        <a href="{{ route('web.audit-logs.index') }}" class="stat-card-modern block border-brand-200/50 hover:border-brand-500/50 hover:bg-white/90 group transition-all">
            <div class="flex items-center justify-between mb-6">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-brand-600">Security</span>
                <div class="stat-icon bg-gray-900 text-white shadow-xl group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <span class="stat-value-modern" data-count-target="{{ $stats['audit_logs'] ?? 0 }}">0</span>
            <span class="inline-flex items-center gap-1 text-[10px] font-black text-brand-600 uppercase tracking-widest mt-4">Security Protocol Active →</span>
        </a>
        @endif
    </div>
</section>

{{-- ── Data Visualization Layer ── --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-10 overflow-hidden">
    {{-- Chart Terminal --}}
    <div class="card xl:col-span-8 p-8 relative overflow-hidden group">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-brand-500/5 blur-[120px] pointer-events-none group-hover:bg-brand-500/10 transition-all duration-1000"></div>
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10 border-b border-gray-100/50 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-8 bg-brand-500 rounded-full"></div>
                <div>
                    <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Growth Matrix</h2>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Operational Revenue vs Expenditure</p>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white/40 p-1.5 rounded-2xl border border-white/40">
                <span class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-500 px-3 py-1.5 rounded-xl bg-white shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-brand-500 shadow-sm"></span> Revenue
                </span>
                <span class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-gray-500 px-3">
                    <span class="w-2 h-2 rounded-full bg-gray-900 shadow-sm"></span> Expense
                </span>
            </div>
        </div>
        
        <div class="relative h-[340px] pointer-events-auto">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Side Summary Matrix --}}
    <div class="xl:col-span-4 flex flex-col gap-6">
        {{-- Revenue Master Card --}}
        <div class="glass-card-dark p-8 flex flex-col justify-between h-[180px] hover:shadow-2xl hover:scale-[1.01] transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-black text-brand-400 uppercase tracking-[0.3em]">Total Revenue Injection</span>
                    <h2 class="text-4xl font-black text-white tracking-tighter mt-2">${{ number_format(array_sum($revenueData ?? []), 0) }}</h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-brand-400 border border-white/10 group-hover:bg-brand-500 group-hover:text-white transition-all duration-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Live Node Activity
            </div>
        </div>

        {{-- Expense Analytics Card --}}
        <div class="glass-card p-8 flex flex-col justify-between h-[180px] hover:shadow-2xl hover:scale-[1.01] transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Expenditure Matrix</span>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tighter mt-2">${{ number_format(array_sum($expenseData ?? []), 0) }}</h2>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 border border-rose-100 group-hover:bg-rose-500 group-hover:text-white transition-all duration-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="text-[10px] font-bold text-rose-500 uppercase tracking-widest flex items-center gap-2 italic">
                Verified Allocation
            </div>
        </div>

    </div>
</div>

{{-- ── Activity Intelligence ── --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-10">
    @if (isset($recentSales) && $recentSales->isNotEmpty())
    <section class="flex flex-col gap-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.4em]">Sales Ledger</h2>
            <a href="{{ route('web.sales.index') }}" class="text-[10px] font-black text-brand-500 uppercase tracking-widest hover:text-brand-700 transition-colors">Access All Assets →</a>
        </div>
        <div class="table-card backdrop-blur-3xl bg-white/40">
            <table class="table-modern border-separate border-spacing-0">
                <thead>
                    <tr>
                        <th class="border-b border-gray-100/50">Transaction ID</th>
                        <th class="border-b border-gray-100/50">Node Date</th>
                        <th class="border-b border-gray-100/50 text-right">Value Asset</th>
                        <th class="border-b border-gray-100/50 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50/50">
                    @foreach($recentSales as $sale)
                    <tr class="group hover:bg-white/60 transition-all cursor-pointer">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-gray-900 text-white flex items-center justify-center text-[10px] font-black">TX</span>
                                <span class="font-black text-gray-900 tracking-tight">{{ $sale->sale_number }}</span>
                            </div>
                        </td>
                        <td class="text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $sale->sale_date->format('M d, Y') }}</td>
                        <td class="text-right">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($sale->total, 2) }}</span>
                            <span class="text-[10px] font-bold text-gray-400 ml-1">{{ $sale->currency }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.sales.show', $sale) }}" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-300 hover:text-brand-500 hover:bg-brand-50 transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    @if(auth()->user()->isAdmin() && isset($recentLogs) && $recentLogs->isNotEmpty())
    {{-- Security Activity card --}}
    <section class="flex flex-col gap-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.4em]">Security Activity</h2>
            <a href="{{ route('web.audit-logs.index') }}" class="text-[10px] font-black text-brand-500 uppercase tracking-widest hover:text-brand-700 transition-colors">Registry Details →</a>
        </div>
        <div class="glass-card-dark p-6 flex flex-col justify-between h-full min-h-[180px] hover:shadow-2xl transition-all group">
            <div class="space-y-4">
                @foreach($recentLogs as $log)
                <div class="flex items-start gap-4 border-l-2 border-brand-500/30 pl-4 py-2 hover:border-brand-500 transition-colors cursor-help group/item">
                    <div class="flex-1">
                        <p class="text-[11px] font-bold text-white tracking-tight leading-normal">
                            <span class="text-brand-400 uppercase tracking-tighter">{{ $log->user->name ?? 'System' }}</span>
                            <span class="text-gray-400 mx-1">/</span>
                            {{ $log->action }}
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="w-1 h-1 rounded-full bg-brand-500"></span>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (isset($recentRefunds) && $recentRefunds->isNotEmpty())
    <section class="flex flex-col gap-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-[10px] font-black text-gray-900 uppercase tracking-[0.4em]">Refund Registry</h2>
            <a href="{{ route('web.refunds.index') }}" class="text-[10px] font-black text-brand-500 uppercase tracking-widest hover:text-brand-700 transition-colors">Access Registry →</a>
        </div>
        <div class="table-card backdrop-blur-3xl bg-white/40">
            <table class="table-modern border-separate border-spacing-0">
                <thead>
                    <tr>
                        <th class="border-b border-gray-100/50">Refund ID</th>
                        <th class="border-b border-gray-100/50">Timestamp</th>
                        <th class="border-b border-gray-100/50 text-right">Value Adjustment</th>
                        <th class="border-b border-gray-100/50 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50/50">
                    @foreach($recentRefunds as $refund)
                    <tr class="group hover:bg-white/60 transition-all cursor-pointer">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-[10px] font-black">RF</span>
                                <span class="font-black text-gray-900 tracking-tight">{{ $refund->refund_number }}</span>
                            </div>
                        </td>
                        <td class="text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $refund->refund_date->format('M d, Y') }}</td>
                        <td class="text-right">
                            <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($refund->total, 2) }}</span>
                            <span class="text-[10px] font-bold text-gray-400 ml-1">{{ $refund->currency }}</span>
                        </td>
                        <td>
                            <a href="{{ route('web.refunds.show', $refund) }}" class="w-10 h-10 rounded-xl flex items-center justify-center text-gray-300 hover:text-brand-500 hover:bg-brand-50 transition-all opacity-0 group-hover:opacity-100">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif
</div>

{{-- ── Data Visualization Engine ── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic Count Engine
    document.querySelectorAll('[data-count-target]').forEach(function(el) {
        let target = parseInt(el.getAttribute('data-count-target'), 10);
        if (isNaN(target)) return;
        let duration = 1500;
        let start = null;
        function step(timestamp) {
            if (!start) start = timestamp;
            let progress = Math.min((timestamp - start) / duration, 1);
            let eased = 1 - Math.pow(1 - progress, 5); // Quint easing
            el.textContent = Math.floor(eased * target).toLocaleString();
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target.toLocaleString();
        }
        requestAnimationFrame(step);
    });

    // Chart.js - Ultra Glass Rendering
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels ?? []) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueData ?? []) !!},
                    backgroundColor: '#E8613C', // Brand Primary
                    borderRadius: { topLeft: 12, topRight: 12, bottomLeft: 4, bottomRight: 4 },
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7,
                    hoverBackgroundColor: '#D4461E'
                }, {
                    label: 'Expenses',
                    data: {!! json_encode($expenseData ?? []) !!},
                    backgroundColor: '#111827', // Gray 900
                    borderRadius: { topLeft: 12, topRight: 12, bottomLeft: 4, bottomRight: 4 },
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7,
                    hoverBackgroundColor: '#000'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                        titleColor: '#111827',
                        bodyColor: '#4B5563',
                        borderColor: 'rgba(0,0,0,0.05)',
                        borderWidth: 1,
                        cornerRadius: 20,
                        padding: 16,
                        boxPadding: 8,
                        usePointStyle: true,
                        callbacks: {
                            labelColor: function(context) {
                                return {
                                    borderColor: 'transparent',
                                    backgroundColor: context.dataset.backgroundColor,
                                    borderRadius: 5
                                };
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#9CA3AF', font: { size: 10, weight: 'bold', family: 'Inter' }, padding: 10 }
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                        border: { display: false },
                        ticks: { 
                            color: '#9CA3AF', 
                            font: { size: 10, weight: 'bold', family: 'Inter' },
                            padding: 10,
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endsection
