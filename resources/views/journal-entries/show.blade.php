@extends('layouts.app')
@section('title', 'Fiscal Entry')

@section('content')
<a href="{{ route('web.journal-entries.index') }}" class="link-back reveal">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
    Journal Matrix
</a>

<div class="max-w-3xl">
    <div class="mb-8 reveal">
        <h1 class="heading-1 mb-3">Journal Entry Details</h1>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $journalEntry->entry_date->format('M d, Y') }}</span>
            <span class="text-gray-200">·</span>
            <span class="text-sm font-bold text-gray-700">{{ $journalEntry->description }}</span>
        </div>
    </div>

    <div class="table-card reveal">
        <div class="px-8 py-5 border-b border-gray-100/30">
            <h2 class="heading-3">Ledger Lines</h2>
        </div>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Account Node</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->lines as $line)
                <tr class="group hover:bg-white/40 transition-all">
                    <td>
                        <span class="font-mono text-xs text-gray-400 tracking-widest mr-2">{{ $line->account->code }}</span>
                        <span class="font-black text-gray-900 tracking-tighter">{{ $line->account->name }}</span>
                    </td>
                    <td class="text-right tabular-nums font-bold {{ $line->debit > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                    <td class="text-right tabular-nums font-bold {{ $line->credit > 0 ? 'text-rose-500' : 'text-gray-300' }}">{{ $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
