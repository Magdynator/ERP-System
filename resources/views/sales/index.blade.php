@extends('layouts.app')
@section('title', 'Sales Matrix')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Sales</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Global revenue ledger & customer transactions</p>
    </div>
    <a href="{{ route('web.sales.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Execute New Sale
    </a>
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Reference ID</th>
                <th>Timestamp</th>
                <th>Client/Entity</th>
                <th class="text-right">Total Valuation</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $s)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 font-black text-gray-900 tracking-tighter">{{ $s->sale_number }}</td>
                <td class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $s->sale_date->format('M d, Y') }}</td>
                <td class="px-6 py-5 font-bold text-gray-700">{{ $s->customer_name ?? 'Anonymous Client' }}</td>
                <td class="px-6 py-5 text-right font-black text-gray-900 tracking-tight">
                    {{ number_format($s->total, 2) }} <span class="text-[10px] text-gray-400 uppercase ml-1">{{ $s->currency }}</span>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.sales.show', $s) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Analyze →</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Zero Sales Recorded</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">No transaction data available in the current ledger.</p>
                    <a href="{{ route('web.sales.create') }}" class="btn-primary mt-8">Initiate Sale</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($sales->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $sales->links() }}
    </div>
    @endif
</div>
@endsection
