@extends('layouts.app')
@section('title', 'Transaction ' . $sale->sale_number)

@section('content')
<a href="{{ route('web.sales.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Sales Matrix
</a>

<div class="max-w-3xl">
    <div class="flex items-start justify-between mb-8 reveal">
        <div>
            <h1 class="heading-1 mb-3">{{ $sale->sale_number }}</h1>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $sale->sale_date->format('M d, Y') }}</span>
                <span class="text-gray-200">·</span>
                <span class="text-sm font-bold text-gray-700">{{ $sale->customer_name ?? 'Anonymous Client' }}</span>
                <span class="status-badge status-neutral">{{ $sale->status }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('web.sales.invoice', $sale) }}" class="btn-primary py-2.5 px-5 bg-indigo-600 hover:bg-indigo-700 border-indigo-600 hover:border-indigo-700 shadow-lg shadow-indigo-600/20">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                Download Invoice
            </a>
        </div>
    </div>

    <div class="table-card mb-8 reveal">
        <div class="px-8 py-5 border-b border-gray-100/30">
            <h2 class="heading-3">Line Items</h2>
        </div>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Product / Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr class="group hover:bg-white/40 transition-all">
                    <td class="font-black text-gray-900 tracking-tighter">Item #{{ $item->id }}</td>
                    <td class="text-right tabular-nums font-bold">{{ $item->quantity }}</td>
                    <td class="text-right tabular-nums text-gray-400">{{ number_format($item->selling_price, 2) }}</td>
                    <td class="text-right font-black text-gray-900 tabular-nums">{{ number_format($item->quantity * $item->selling_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="detail-total">
            <span class="text-[10px] text-gray-400 uppercase tracking-widest mr-2">Total Valuation</span>
            {{ number_format($sale->total, 2) }} {{ $sale->currency }}
        </div>
    </div>

    @if($sale->payments->count() > 0)
    <div class="card p-8 reveal">
        <h2 class="heading-3 mb-6">Payment Streams</h2>
        <div class="flex flex-col gap-3">
            @foreach($sale->payments as $p)
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/40 border border-white/30 hover:bg-white/60 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75"/></svg>
                    </div>
                    <div class="flex-1">
                        <span class="font-black text-gray-900">{{ number_format($p->amount, 2) }} {{ $sale->currency }}</span>
                        <span class="text-gray-300 mx-2">·</span>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $p->method }}</span>
                    </div>
                    @if($p->reference)
                        <span class="text-[10px] text-gray-400 font-mono tracking-widest uppercase">{{ $p->reference }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
