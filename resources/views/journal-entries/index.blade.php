@extends('layouts.app')
@section('title', 'Journal Matrix')

@section('content')
<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Journal Entries</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Double-entry bookkeeping ledger & fiscal event registry</p>
    </div>
    <a href="{{ route('web.journal-entries.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Commit New Entry
    </a>
</div>

<div class="table-card reveal mt-8">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Fiscal Date</th>
                <th>Operation Narrative</th>
                <th class="text-right w-32">Operations</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $e)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 text-xs font-bold text-gray-400 uppercase tracking-tight">{{ $e->entry_date->format('M d, Y') }}</td>
                <td class="px-6 py-5">
                    <div class="font-bold text-gray-700 tracking-tight">{{ $e->description }}</div>
                </td>
                <td class="px-6 py-5 text-right">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.journal-entries.show', $e) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest transition-colors">Analyze →</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="empty-state py-20 text-center">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter">Fiscal Ledger Empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Create your first entry node to begin bookkeeping operations.</p>
                    <a href="{{ route('web.journal-entries.create') }}" class="btn-primary mt-8">Commit Entry</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($entries->hasPages())
    <div class="p-6 border-t border-gray-100/30">
        {{ $entries->links() }}
    </div>
    @endif
</div>
@endsection
