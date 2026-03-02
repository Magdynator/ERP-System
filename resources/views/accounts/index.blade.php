@extends('layouts.app')
@section('title', 'Accounts')
@section('content')

<div class="page-header reveal">
    <div class="page-title-section">
        <h1 class="heading-1">Accounts</h1>
        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">Chart of accounts & bookkeeping node</p>
    </div>
    <a href="{{ route('web.accounts.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Provision Account
    </a>
</div>

@if(request('type'))
    <div class="inline-flex items-center gap-2 mb-8 px-4 py-2 rounded-2xl bg-brand-50 text-xs text-brand-700 font-black uppercase tracking-widest border border-brand-100 reveal">
        Filter Applied: {{ request('type') }}
        <a href="{{ route('web.accounts.index') }}" class="w-5 h-5 flex items-center justify-center rounded-lg hover:bg-brand-200 transition-colors" title="Clear filter">✕</a>
    </div>
@endif

<div class="table-card reveal">
    <table class="table-modern">
        <thead>
            <tr>
                <th>Code</th>
                <th>Identity</th>
                <th>Classification</th>
                <th>Status</th>
                <th class="text-right w-32">Control</th>
            </tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
            <tr class="group hover:bg-white/40 transition-all">
                <td class="px-6 py-5 font-black text-gray-900 tracking-tighter">{{ $a->code }}</td>
                <td class="px-6 py-5 font-bold text-gray-700">{{ $a->name }}</td>
                <td class="px-6 py-5"><span class="status-badge status-neutral">{{ $a->typeLabel() }}</span></td>
                <td class="px-6 py-5">
                    @if($a->is_active)
                        <span class="status-badge status-active">Active Node</span>
                    @else
                        <span class="status-badge status-inactive">Offline</span>
                    @endif
                </td>
                <td class="px-6 py-5">
                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('web.accounts.edit', $a) }}" class="text-xs font-black text-brand-500 hover:text-brand-700 uppercase tracking-widest">Edit</a>
                        <form action="{{ route('web.accounts.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this account?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-black text-rose-500 hover:text-rose-700 uppercase tracking-widest">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty-state py-20">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-6 text-gray-300">
                         <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 tracking-tighter">Database empty</h3>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-2">No accounts recorded in the ledger.</p>
                    <a href="{{ route('web.accounts.create') }}" class="btn-primary mt-8">Configure First Account</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($accounts->hasPages())
    <div class="p-6 border-t border-gray-100/50">
        {{ $accounts->links() }}
    </div>
    @endif
</div>
@endsection
