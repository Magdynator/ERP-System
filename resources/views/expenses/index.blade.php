@extends('layouts.app')
@section('title', 'Outflow Matrix')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Expenses</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Expenditure tracking & resource allocation ledger</p>
    </div>
    <a href="{{ route('web.expenses.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Execute New Outflow
    </a>
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Classification</th>
                <th>Entity/Vendor</th>
                <th class="text-right">Valuation</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $e)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $e->expense_date->format('M d, Y') }}</td>
                <td class="px-6 py-5">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest bg-gray-100 px-2 py-1 rounded-lg">
                        {{ $e->category->name ?? 'Unclassified' }}
                    </span>
                </td>
                <td class="px-6 py-5 font-bold text-gray-700">{{ $e->vendor_name ?? 'Anonymous Entity' }}</td>
                <td class="px-6 py-5 text-right font-black text-rose-500 tracking-tight">
                    <span class="text-gray-400 text-[10px] mr-1 uppercase">Debit</span> {{ number_format($e->amount, 2) }}
                    <span class="text-[10px] text-gray-400 uppercase ml-1">{{ $e->currency }}</span>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.expenses.show', $e) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Analyze</a>
                        <a href="{{ route('web.expenses.edit', $e) }}" class="text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest transition-colors">Modify</a>
                        <form action="{{ route('web.expenses.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('Archive this record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-colors">Archive</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Outflow Ledger Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">No expenditure data available in the current ledger.</p>
                    <a href="{{ route('web.expenses.create') }}" class="btn-primary mt-8">Initiate Record</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($expenses->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $expenses->links() }}
    </div>
    @endif
</div>
@endsection
