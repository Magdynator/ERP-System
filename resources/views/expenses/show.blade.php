@extends('layouts.app')
@section('title', 'Outflow Record')

@section('content')
<a href="{{ route('web.expenses.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Outflow Matrix
</a>

<div class="max-w-2xl">
    <h1 class="heading-1 mb-2 reveal">Expense Details</h1>
    <div class="flex items-center gap-3 mb-8 reveal">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $expense->expense_date->format('M d, Y') }}</span>
        <span class="text-gray-200">·</span>
        <span class="status-badge status-neutral">{{ $expense->category->name ?? 'Unclassified' }}</span>
    </div>

    <div class="card reveal">
        <dl class="detail-grid">
            <div class="detail-item">
                <dt class="detail-label">Timestamp</dt>
                <dd class="detail-value">{{ $expense->expense_date->format('M d, Y') }}</dd>
            </div>
            <div class="detail-item">
                <dt class="detail-label">Classification</dt>
                <dd class="detail-value">{{ $expense->category->name ?? 'Unclassified' }}</dd>
            </div>
            <div class="detail-item">
                <dt class="detail-label">Valuation</dt>
                <dd class="detail-value text-2xl font-black text-rose-500 tracking-tight">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</dd>
            </div>
            <div class="detail-item">
                <dt class="detail-label">Entity / Vendor</dt>
                <dd class="detail-value">{{ $expense->vendor_name ?? 'Anonymous Entity' }}</dd>
            </div>
            @if($expense->description)
            <div class="detail-item col-span-2">
                <dt class="detail-label">Operation Narrative</dt>
                <dd class="detail-value text-gray-600 font-medium">{{ $expense->description }}</dd>
            </div>
            @endif
        </dl>
        <div class="px-8 py-5 border-t border-gray-100/30 flex gap-3">
            <a href="{{ route('web.expenses.edit', $expense) }}" class="btn-primary">Modify Record</a>
            <a href="{{ route('web.expenses.index') }}" class="btn-ghost">Return to Matrix</a>
        </div>
    </div>
</div>
@endsection
