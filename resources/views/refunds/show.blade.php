@extends('layouts.app')
@section('title', 'Reversal ' . $refund->refund_number)

@section('content')
<a href="{{ route('web.refunds.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Return Matrix
</a>

<div class="max-w-3xl">
    <div class="mb-8 reveal">
        <h1 class="heading-1 mb-3">{{ $refund->refund_number }}</h1>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $refund->refund_date->format('M d, Y') }}</span>
            <span class="status-badge status-neutral">{{ $refund->status }}</span>
        </div>
    </div>

    <div class="table-card reveal">
        <div class="px-8 py-5 border-b border-gray-100/30">
            <h2 class="heading-3">Reversed Line Items</h2>
        </div>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Item Reference</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refund->items as $item)
                <tr class="group hover:bg-white/40 transition-all">
                    <td class="font-black text-gray-900 tracking-tighter">#{{ $item->sale_item_id }}</td>
                    <td class="text-right tabular-nums font-bold">{{ $item->quantity }}</td>
                    <td class="text-right tabular-nums text-gray-400">{{ number_format($item->selling_price, 2) }}</td>
                    <td class="text-right font-black text-amber-600 tabular-nums">{{ number_format($item->quantity * $item->selling_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="detail-total">
            <span class="text-[10px] text-gray-400 uppercase tracking-widest mr-2">Credit Total</span>
            {{ number_format($refund->total, 2) }} {{ $refund->currency }}
        </div>
    </div>
</div>
@endsection
