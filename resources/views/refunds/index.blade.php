@extends('layouts.app')
@section('title', 'Return Matrix')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Refunds</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Reversal tracking & adjustment registry</p>
    </div>
    <a href="{{ route('web.refunds.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Execute New Reversal
    </a>
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Reversal ID</th>
                <th>Timestamp</th>
                <th class="text-right">Adjustment Value</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($refunds as $r)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 font-black text-gray-900 tracking-tighter">{{ $r->refund_number }}</td>
                <td class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $r->refund_date->format('M d, Y') }}</td>
                <td class="px-6 py-5 text-right font-black text-amber-600 tracking-tight">
                    <span class="text-gray-400 text-[10px] mr-1 uppercase">Credit</span> {{ number_format($r->total, 2) }}
                    <span class="text-[10px] text-gray-400 uppercase ml-1">{{ $r->currency }}</span>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.refunds.show', $r) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Analyze →</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Refund Registry Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">No reversal data available in the current ledger.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($refunds->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $refunds->links() }}
    </div>
    @endif
</div>
@endsection
